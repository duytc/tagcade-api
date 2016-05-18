<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout (in seconds) to process for each publisher or ad network. Set to -1 to disable timeout', -1)
            ->setDescription('Daily rotate report')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $timeout = $input->getOption('timeout');
        if ($timeout == -1) {
            $timeout = null;
        }
        // run the command with -vv verbosity to show messages
        // https://symfony.com/doc/current/cookbook/logging/monolog_console.html
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $dailyReportCreator = $container->get('tagcade.service.report.performance_report.display.creator.daily_report_creator');

        /* create performance and billing reports */
        $logger->info('start daily rotate');

        // Creating network hierarchy reports
        $allAdNetworks = $adNetworkManager->all();
        $this->rotateNetworkReports($allAdNetworks, $timeout, $logger);
        unset($allAdNetworks);

        // Creating accounts reports
        $allPublishers = $publisherManager->allActivePublishers();
        $this->rotateAccountReports($allPublishers, $timeout, $logger);

        // Creating platform reports
        $reportDate = new DateTime('yesterday');
        $dailyReportCreator->createPlatformReport($reportDate);

        $dailyReportCreator->createSegmentReports();
        $dailyReportCreator->createRonSlotReports();

        $logger->info('finished daily rotation');

        $this->updateBilledAmountThreshold($allPublishers, $timeout, $logger);

        $this->createPerformanceReportForPartner($allPublishers,$timeout,$logger);

    }

    protected function createPerformanceReportForPartner(array $publishers, $timeout, LoggerInterface $logger)
    {
        $logger->info('Start updating performance report for partner');

        foreach($publishers as $publisher){
            if(!$publisher instanceof PublisherInterface){
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('Start updating performance report for partner of publisher %d',$id));

            $cmd = sprintf('%s tc:report:create-partner-report --publisher %d', $this->getAppConsoleCommand(), $id);
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('Finish updating performance report for partner of publisher %d',$id));
        }

        $logger->info('Finish updating performance report for partner');
    }


    protected function updateBilledAmountThreshold(array $publishers, $timeout, LoggerInterface $logger)
    {
        $logger->info('Starting to update threshold billed amount');

        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('Start updating threshold billed amount for publisher %d', $id));

            $cmd = sprintf('%s tc:billing:update-threshold --id %d', $this->getAppConsoleCommand(), $id);
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('Finished updating threshold billed amount for publisher %d', $id));

        }

        $logger->info('finished update threshold billed amount');
    }

    protected function rotateNetworkReports(array $allAdNetworks, $timeout, LoggerInterface $logger)
    {
        foreach ($allAdNetworks as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $id = $adNetwork->getId();
            $logger->info(sprintf('start daily rotate for ad network %d', $id));

            $cmd = sprintf('%s tc:report:daily-rotate:ad-network --id %d', $this->getAppConsoleCommand(), $id);
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished daily rotate for ad network %d', $id));
        }
    }

    protected function rotateAccountReports(array $publishers, $timeout, LoggerInterface $logger)
    {
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('start daily rotate for publisher %d', $id));

            $cmd = sprintf('%s tc:report:daily-rotate:account --id %d', $this->getAppConsoleCommand(), $id);
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished daily rotate for publisher %d', $id));
        }
    }

    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getContainer()->getParameter('kernel.root_dir');

        return sprintf('php %s/console', $pathToSymfonyConsole);
    }

    protected function executeProcess(Process $process, array $options, LoggerInterface $logger)
    {
        if (array_key_exists('timeout', $options)) {
            $process->setTimeout($options['timeout']);
        }

        $process->mustRun(function($type, $buffer) use($logger) {
                if (Process::ERR === $type) {
                    $logger->error($buffer);
                } else {
                    $logger->info($buffer);
                }
            }
        );
    }
}
