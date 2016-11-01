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
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class VideoDailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-report:daily-rotate')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to rotate data')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('skip-update-billing', null, InputOption::VALUE_NONE, 'check whether to update billing threshold or not')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout (in seconds) to process for each publisher or ad network. Set to -1 to disable timeout', -1)
            ->setDescription('Video daily rotate report');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
        $dailyReportCreator = $container->get('tagcade.service.report.video_report.creator.daily_report_creator');
        $allPublishers = $publisherManager->allPublisherWithVideoModule();

        if (empty($allPublishers)) {
            $logger->info('There\'s no publisher having video module');
            return;
        }

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

        $skipUpdateBillingThreshold = filter_var($input->getOption('skip-update-billing'), FILTER_VALIDATE_BOOLEAN) ;
        $override = filter_var($input->getOption('force'), FILTER_VALIDATE_BOOLEAN);

        $logger->info('start daily rotation for video');

        // Creating accounts reports
        $this->rotateAccountReports($date, $allPublishers, $timeout, $logger, $override);

        // create demand partner reports
        $videoDemandPartners = $videoDemandPartnerManager->all();
        $this->rotateNetworkReports($date, $videoDemandPartners, $timeout, $logger, $override);
        unset($videoDemandPartners);

        $dailyReportCreator->setReportDate($date);
        $dailyReportCreator->createPlatformReport($date, $override);

        if ($skipUpdateBillingThreshold === false) {
            $this->updateBilledAmountThreshold($allPublishers, $timeout, $logger);
        }

        $logger->info('finished daily rotation for video');
    }

    protected function updateBilledAmountThreshold(array $publishers, $timeout, LoggerInterface $logger)
    {
        $logger->info('Starting to update video threshold billed amount');

        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('Start updating threshold billed amount for publisher %d', $id));

            $cmd = sprintf('%s tc:billing:update-video-threshold --id %d -vvv', $this->getAppConsoleCommand(), $id);
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('Finished updating video threshold billed amount for publisher %d', $id));

        }

        $logger->info('finished update video threshold billed amount');
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

    protected function rotateAccountReports(DateTime $date, array $publishers, $timeout, LoggerInterface $logger, $override = false)
    {
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $id = $publisher->getId();
            $logger->info(sprintf('start video daily rotate for publisher %d', $id));

            $cmd = sprintf('%s tc:video-report:daily-rotate:account --id %d --date %s %s -vvv', $this->getAppConsoleCommand(), $id, $date->format('Y-m-d'), $override === true ? '--force' : '');
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished video daily rotate for publisher %d', $id));
        }
    }

    protected function rotateNetworkReports(DateTime $date, array $videoDemandPartners, $timeout, LoggerInterface $logger, $override = false)
    {
        foreach ($videoDemandPartners as $videoDemandPartner) {
            if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
                continue;
            }

            $id = $videoDemandPartner->getId();
            $logger->info(sprintf('start daily rotate for video demand partner %d', $id));

            $cmd = sprintf('%s tc:video-report:daily-rotate:demand-partner --id %d --date %s %s -vvv', $this->getAppConsoleCommand(), $id, $date->format('Y-m-d'), $override === true ? '--force' : '');
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);

            $logger->info(sprintf('finished daily rotate for video demand partner %d', $id));
        }
    }
}