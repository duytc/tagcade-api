<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdNetworkReport;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\AdTagReport;
use Tagcade\Entity\Report\PerformanceReport\Display\AdNetwork\SiteReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;

class DailyAdNetworkRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate:ad-network')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'the date to rotate data')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'ad network id')
            ->setDescription('Daily rotate ad network report.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $id = $input->getOption('id');
        $date = $input->getOption('date');
        $override = filter_var($input->getOption('force'), FILTER_VALIDATE_BOOLEAN);

        if (empty($date)) {
            $date = new DateTime('yesterday');
        } else if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            throw new InvalidArgumentException('expect date format to be "YYYY-MM-DD"');
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $date);
        }

        if ($date->setTime(0,0,0) == new DateTime('today')) {
            $logger->error(sprintf('can not create today report for publisher %d', $id));
            return;
        }

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $reportCreator = $container->get('tagcade.service.report.performance_report.display.creator.report_creator');
        $adNetworkManager = $container->get('tagcade.domain_manager.ad_network');
        $adNetworkReportRepository = $container->get('tagcade.repository.report.performance_report.display.hierarchy.ad_network.ad_network');

        $adNetwork = $adNetworkManager->find($id);
        if (!$adNetwork instanceof AdNetworkInterface) {
            $logger->error(sprintf('ad network %d does not exist', $id));
            return;
        }

        $report = current($adNetworkReportRepository->getReportFor($adNetwork, $date, $date));
        if ($report instanceof ReportInterface && $override === false) {
            $logger->error(sprintf('report for ad network %d on %s is already existed, use "--force" option to override', $id, $date->format('Y-m-d')));
            return;
        }

        $reportCreator->setDate($date);
        /* create performance and billing reports */
        $logger->info('start daily rotate for performance');
        /**
         * @var AdNetworkReport $adNetworkReport$networkSiteReports
         */
        $adNetworkReport = $reportCreator->getReport(
            new AdNetworkReportType($adNetwork)
        );

        if ($override === true && $report instanceof ReportInterface) {
            $logger->info(sprintf('Persisting report for ad network %s', $id));
            $this->overrideReport($report, $adNetworkReport, $entityManager);
            $logger->info(sprintf('Flushing report for ad network %s', $id));
            $logger->info('finished daily rotation');
            $entityManager->clear();
            gc_collect_cycles();
            unset($adNetworkReport);
            return;
        }

        $logger->info(sprintf('Persisting report for ad network %s', $id));
        $entityManager->persist($adNetworkReport);
        $logger->info(sprintf('Flushing report for ad network %s', $id));
        $entityManager->flush();
        $entityManager->clear();
        gc_collect_cycles();
        unset($adNetworkReport);
        $logger->info('finished daily rotation');
    }

    protected function overrideReport(ReportInterface $oldReport, AdNetworkReportInterface $report, EntityManagerInterface $em)
    {
        $adTagReportRepository = $em->getRepository(AdTagReport::class);
        $siteReportRepository = $em->getRepository(SiteReport::class);
        $adNetworkReportRepository = $em->getRepository(AdNetworkReport::class);

        $siteReports = $report->getSubReports();
        foreach($siteReports as $siteReport) {
            $adTagReports = $siteReport->getSubReports();
            foreach($adTagReports as $adTagReport) {
                $adTagReportRepository->overrideReport($adTagReport);
            }
            $siteReportRepository->overrideReport($oldReport, $siteReport);
        }
        $adNetworkReportRepository->overrideReport($report);
    }
}
