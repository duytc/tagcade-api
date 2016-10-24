<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RtbDailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:rtb-report:daily-rotate')
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

        /* create rtb reports */
        $logger->info('start daily rotation for rtb');
        $dailyRtbReportCreator = $this->getContainer()->get('tagcade.service.report.rtb_report.creator.daily_report_creator');

        $reportDate = new DateTime('yesterday');
        $dailyRtbReportCreator->setReportDate($reportDate);

        $dailyRtbReportCreator->createAndSave($allPublishers);
        $logger->info('finished daily rotation for rtb');
    }
}
