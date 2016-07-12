<?php
namespace Tagcade\Bundle\AppBundle\Command;
use DateInterval;
use DatePeriod;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface;

class RecalculatePublisherUnifiedReportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:unified-report:recalculate-publisher-report')
            ->addOption('publisher', null, InputOption::VALUE_REQUIRED, 'publisher id add data')
            ->addOption('start-date', null, InputOption::VALUE_REQUIRED, 'start date with format yyyy-mm-dd')
            ->addOption('end-date', null, InputOption::VALUE_OPTIONAL, 'end date with format yyyy-mm-dd')
            ->addOption('force', null, InputOption::VALUE_NONE, 'push all changes to database')
            ->setDescription('recalculate publisher report from sub reports');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ReportBuilderInterface $reportBuilder */
        $reportBuilder = $this->getContainer()->get('tagcade.service.report.unified_report.selector.report_builder');

        $force = $input->getOption('force');
        $publisherId = $input->getOption('publisher');
        $publisher = $this->getContainer()->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('The publisher "%d" does not exist'));
        }

        $startDate = $input->getOption('start-date');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            throw new \Exception('"start-date" format should be yyyy-mm-dd');
        }
        $startDate = new \DateTime($startDate);

        $endDate = $input->getOption('end-date');
        if (empty($endDate)) {
            $endDate = new \DateTime('today');
        } else {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                throw new \Exception('"end-date" format should be yyyy-mm-dd');
            }
            $endDate = new \DateTime($endDate);
        }

        $endDate = $endDate->modify('+1 day');
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval ,$endDate);

        $reports = [];
        foreach($dateRange as $date) {
            $publisherReport = $reportBuilder->getAllDemandPartnersByPartnerReport(
                $publisher,
                new Params($date, $date, true, true)
            );

            if (!$publisherReport) {
                continue;
            }

            $publisherReport1 = $reportBuilder->getAllDemandPartnersByDayReport(
                $publisher,
                new Params($date, $date, false, false)
            );

            if ($publisherReport1) {
                $publisherReport1 = $publisherReport1->getReports()[0];
            }

            if ($publisherReport1 instanceof PublisherReport) {
                $output->writeln(sprintf('<info>%s - %s :</info>', $publisher->getUsername(), $date->format('Y-m-d')));
                $output->writeln(sprintf("\tImpressions: %d ---> <info>%d</info>", $publisherReport1->getImpressions(), $publisherReport->getImpressions()));
                $output->writeln(sprintf("\tOpportunities: %d ---> <info>%d</info>", $publisherReport1->getTotalOpportunities(), $publisherReport->getTotalOpportunities()));
                $output->writeln(sprintf("\tPassbacks: %d ---> <info>%d</info>", $publisherReport1->getPassbacks(), $publisherReport->getPassbacks()));
                $output->writeln(sprintf("\tFill Rate: %d ---> <info>%d</info>", $publisherReport1->getFillRate(), $publisherReport->getFillRate()));
                $output->writeln(sprintf("\tEstimated CPM: %d ---> <info>%d</info>", $publisherReport1->getEstCpm(), $publisherReport->getEstCpm()));
                $output->writeln(sprintf("\tEstimated Revenue: %d ---> <info>%d</info>", $publisherReport1->getEstRevenue(), $publisherReport->getEstRevenue()));
            }

            $report = new PublisherReport();
            $report->setName($publisher->getUsername())
                ->setTotalOpportunities($publisherReport->getTotalOpportunities())
                ->setImpressions($publisherReport->getImpressions())
                ->setPassbacks($publisherReport->getPassbacks())
                ->setFillRate()
                ->setPublisher($publisher)
                ->setDate($date)
                ->setEstRevenue($publisherReport->getEstRevenue())
                ->setEstCpm($publisherReport->getEstCpm())
            ;

            $reports[] = $report;
        }

        if ($force === true) {
            /** @var PublisherReportRepositoryInterface $publisherReportRepository */
            $publisherReportRepository = $this->getContainer()->get('tagcade.repository.report.unified_report.publisher.publisher_report');
            $publisherReportRepository->saveMultipleReport($reports, true);
            $output->writeln('All changes flushed to database');
        }
    }
}