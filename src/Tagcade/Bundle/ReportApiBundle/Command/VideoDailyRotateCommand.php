<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VideoDailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-report:daily-rotate')
            ->setDescription('Video daily rotate report');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $logger->info('start daily rotation for video');

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
        $dailyVideoReportCreator = $this->getContainer()->get('tagcade.service.report.video_report.creator.daily_report_creator');

        /* create video reports */
        $dailyVideoReportCreator
            ->setReportDate(new DateTime('yesterday'))
            ->createAndSave($publisherManager->allActivePublishers(), $videoDemandPartnerManager->all());

        $logger->info('finished daily rotation for video');
    }
}