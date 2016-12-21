<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to rotate data')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('skip-update-billing', null, InputOption::VALUE_NONE, 'check whether to update billing threshold or not')
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

        $date = $input->getOption('date');

        if (empty($date)) {
            $date = new DateTime('yesterday');
        } else if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            throw new InvalidArgumentException('expect date format to be "YYYY-MM-DD"');
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $date);
        }

        if ($date->setTime(0,0,0) == new DateTime('today')) {
            throw new InvalidArgumentException('Can not rotate report for Today');
        }

        $skipUpdateBillingThreshold = filter_var($input->getOption('skip-update-billing'), FILTER_VALIDATE_BOOLEAN) ;
        $override = filter_var($input->getOption('force'), FILTER_VALIDATE_BOOLEAN);

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
        $this->rotateNetworkReports($date, $allAdNetworks, $timeout, $logger, $override);
        unset($allAdNetworks);

        // Creating accounts reports
        $allPublishers = $publisherManager->allActivePublishers();
        $this->rotateAccountReports($date, $allPublishers, $timeout, $logger, $override);

        // Creating header bidding reports
        $this->rotateHeaderBiddingReports($date, $timeout, $logger, $override);

        // creating video reports
        $this->rotateVideoReports($date, $timeout, $logger, $override);

        // Creating platform reports
        $dailyReportCreator->setReportDate($date);
        $dailyReportCreator->createPlatformReport($date, $override);
        $dailyReportCreator->createSegmentReports($autoFlush = true, $override = true);
        $dailyReportCreator->createRonSlotReports();

        $logger->info('finished daily rotation');

        if ($skipUpdateBillingThreshold === false) {
            $this->updateBilledAmountThreshold($allPublishers, $timeout, $logger);
        }

        $this->createPerformanceReportForPartner($date, $allPublishers, $timeout, $logger, $override);
    }

    protected function createPerformanceReportForPartner(DateTime $date, array $publishers, $timeout, LoggerInterface $logger, $override = false)
    {
        $logger->info('Start updating performance report for partner');

        foreach($publishers as $publisher){
            if(!$publisher instanceof PublisherInterface){
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('Start updating performance report for partner of publisher %d',$id));

            $cmd = sprintf('%s tc:report:create-partner-report --publisher %d --start-date %s %s', $this->getAppConsoleCommand(), $id, $date->format('Y-m-d'), $override === true ? '--override' : '');
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

    protected function rotateNetworkReports(DateTime $date, array $allAdNetworks, $timeout, LoggerInterface $logger, $override = false)
    {
        foreach ($allAdNetworks as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $id = $adNetwork->getId();
            $logger->info(sprintf('start daily rotate for ad network %d', $id));

            $cmd = sprintf('%s tc:report:daily-rotate:ad-network --id %d --date %s %s', $this->getAppConsoleCommand(), $id, $date->format('Y-m-d'), $override === true ? '--force' : '');
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished daily rotate for ad network %d', $id));
        }
    }

    protected function rotateHeaderBiddingReports(DateTime $date, $timeout, LoggerInterface $logger, $override = false)
    {
        $logger->info(sprintf('start rotating header bidding report'));

        $cmd = sprintf('%s tc:header-bidding-report:daily-rotate --date %s %s', $this->getAppConsoleCommand(), $date->format('Y-m-d'), $override === true ? '--force' : '');
        $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

        $logger->info(sprintf('finish rotating header bidding report'));
    }

    protected function rotateVideoReports(DateTime $date, $timeout, LoggerInterface $logger, $override = false)
    {
        $logger->info(sprintf('start rotating video report'));

        $cmd = sprintf('%s tc:video-report:daily-rotate --date %s %s', $this->getAppConsoleCommand(), $date->format('Y-m-d'), $override === true ? '--force' : '');
        $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

        $logger->info(sprintf('finish rotating video report'));
    }


    protected function rotateAccountReports(DateTime $date, array $publishers, $timeout, LoggerInterface $logger, $override = false)
    {
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('start daily rotate for publisher %d', $id));

            $cmd = sprintf('%s tc:report:daily-rotate:account --id %d --date %s %s', $this->getAppConsoleCommand(), $id, $date->format('Y-m-d'), $override === true ? '--force' : '');
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished daily rotate for publisher %d', $id));
        }
    }

    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getContainer()->getParameter('kernel.root_dir');
        $environment = $this->getContainer()->getParameter('kernel.environment');
        $debug = $this->getContainer()->getParameter('kernel.debug');

        $command = sprintf('php %s/console --env=%s', $pathToSymfonyConsole, $environment);

        if (!$debug) {
            $command .= ' --no-debug';
        }

        return $command;
    }

    protected function executeProcess(Process $process, array $options, LoggerInterface $logger)
    {
        if (array_key_exists('timeout', $options)) {
            $process->setTimeout($options['timeout']);
        }

        try {
            $process->mustRun(function($type, $buffer) use($logger) {
                    if (Process::ERR === $type) {
                        $logger->error($buffer);
                    } else {
                        $logger->info($buffer);
                    }
                }
            );
        } catch (ProcessFailedException $ex) {
            throw $ex;
        }
    }
}
