<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\TestEventCounter;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\DailyReportCreator;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportCreator;

class GenerateDummyPerformanceReportCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:dummy-performance-report:create')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'Publisher id')
            ->addOption('start-date', 'f', InputOption::VALUE_REQUIRED, 'Start date (YYYY-MM-DD) of the report. ')
            ->addOption('end-date', 't', InputOption::VALUE_OPTIONAL, 'End date of the report (YYYY-MM-DD). Default is yesterday', (new \DateTime('yesterday'))->format('Ymd'))
            ->setDescription('Generate dummy performance report for a publisher');;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startDate = $input->getOption('start-date');
        $endDate = $input->getOption('end-date');
        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $today = new \DateTime('today');

        if ($startDate > $endDate || $endDate >= $today) {
            throw new \Exception('start-date must be less than or equal to end-date and endDate must not exceed today');
        }

        $publisherId = $input->getOption('publisher');
        if (!is_numeric($publisherId) || (int)$publisherId < 1) {
            throw new \Exception(sprintf('Expect positive integer publisher id. The value %s is entered', $publisherId));
        }

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($startDate, $interval, $endDate);

        $container = $this->getContainer();
        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $adSlotManager = $container->get('tagcade.domain_manager.ad_slot');
        $ronAdSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
        $segmentRepository = $container->get('tagcade.repository.segment');
        $adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
        $userManager = $container->get('tagcade_user.domain_manager.publisher');

        $publisher = $userManager->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('Not found publisher with id %d', $publisherId));
        }

        $reportTypes = [
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.ad_tag'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.platform'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.account'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.site'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.platform.ad_slot'),

            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.ad_tag'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.ad_network'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.ad_network.site'),

            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.segment'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.ron_ad_slot'),
            $container->get('tagcade.service.report.performance_report.display.creator.creators.hierarchy.segment.ron_ad_tag')
        ];

        $eventCounter = new TestEventCounter($adSlotManager->getAdSlotsForPublisher($publisher));
        $reportCreator = new ReportCreator($reportTypes, $eventCounter);
        $dailyReportCreator = new DailyReportCreator($em, $reportCreator, $segmentRepository, $ronAdSlotManager);

        foreach ($dateRange as $date) {
            /**
             * @var \DateTime $date
             */
            echo sprintf("%s processing...\n", $date->format('Y-m-d'));

            // TODO fetch report for this date and only create if there is no report
            $eventCounter->refreshTestData();

            $publishers = [$publisher];
            $dailyReportCreator
                ->setReportDate($date)
                ->createAndSave(
                    $publishers,
                    $adNetworkManager->all()
                );

            echo sprintf("%s created \n", $date->format('Y-m-d'));

            $em->flush();

            gc_collect_cycles();
        }
    }
} 