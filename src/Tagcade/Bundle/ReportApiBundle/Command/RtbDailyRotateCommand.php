<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;

class RtbDailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:rtb-report:daily-rotate')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to rotate data')
            ->setDescription('Rtb daily rotate report');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $userManager = $container->get('tagcade_user.domain_manager.publisher');
        $allPublishers = $userManager->allPublisherWithRtbModule();

        if (empty($allPublishers)) {
            $logger->info('There\'s no publisher having RTB module');
            return;
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

        /* create rtb reports */
        $logger->info('start daily rotation for rtb');
        $dailyRtbReportCreator = $this->getContainer()->get('tagcade.service.report.rtb_report.creator.daily_report_creator');

        $dailyRtbReportCreator->setReportDate($date);

        $dailyRtbReportCreator->createAndSave($allPublishers);
        $logger->info('finished daily rotation for rtb');
    }
}
