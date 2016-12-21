<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;

class HeaderBiddingDailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:header-bidding-report:daily-rotate')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to rotate data')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout (in seconds) to process for each publisher or ad network. Set to -1 to disable timeout', -1)
            ->setDescription('Header Bidding daily rotate report');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $allPublishers = $publisherManager->allPublisherWithHeaderBiddingModule();

        if (empty($allPublishers)) {
            $logger->info('There\'re no publisher having header bidding module');
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

        if ($date->setTime(0,0,0) == new DateTime('today')) {
            throw new InvalidArgumentException('Can not rotate report for Today');
        }

        $override = filter_var($input->getOption('force'), FILTER_VALIDATE_BOOLEAN);

        $logger->info('start daily rotation for header bidding');

        $dailyVideoReportCreator = $this->getContainer()->get('tagcade.service.report.header_bidding_report.creator.daily_report_creator');

        /* create header bidding reports */
        $dailyVideoReportCreator
            ->setReportDate($date)
            ->createAndSave($allPublishers, $override);

        $logger->info('finished daily rotation for header bidding');
    }
}