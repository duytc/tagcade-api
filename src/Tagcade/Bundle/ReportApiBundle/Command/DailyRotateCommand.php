<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter;

class DailyRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate')
            ->setDescription('Daily rotate report')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start rotating...');
        $adSlotManager = $this->getContainer()->get('tagcade.domain_manager.ad_slot');
        $eventCounter = new TestEventCounter($adSlotManager->allReportableAdSlots());
        $userManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        $billingEditor = $this->getContainer()->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');

        $reportDate = new DateTime('yesterday');

        /* create performance and billing reports */
        $output->writeln('--> start daily rotation for performance and billing...');
        $dailyReportCreator = $this->getContainer()->get('tagcade.service.report.performance_report.display.creator.daily_report_creator');

        // create report from redis data
        $dailyReportCreator->setReportDate($reportDate);
        $eventCounter->refreshTestData();
        $dailyReportCreator->createAndSave(
            $userManager->allActivePublishers(),
            $adNetworkManager->all()
        );

        // recalculating billed amount
        $output->writeln('finished creating report from redis log, start recalculating billed amount...');

        $updatedCount = $billingEditor->updateBilledAmountThresholdForAllPublishers($reportDate);

        $output->writeln( sprintf('finish recalculating billed amount. Total %d publisher(s) gets updated.', $updatedCount));

        $output->writeln('--> finished daily rotation for performance and billing...');

        /* create rtb reports */
        $output->writeln('--> start daily rotation for rtb...');
        $dailyRtbReportCreator = $this->getContainer()->get('tagcade.service.report.rtb_report.creator.daily_report_creator');

        $reportDate = new DateTime('yesterday');
        $dailyRtbReportCreator->setReportDate($reportDate);

        $dailyRtbReportCreator->createAndSave($userManager->allActivePublishers());
        $output->writeln('--> finished daily rotation for rtb...');

        $output->writeln('finished daily rotation');
    }
}
