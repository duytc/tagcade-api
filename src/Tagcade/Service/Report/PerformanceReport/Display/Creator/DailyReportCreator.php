<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\PlatformReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AggregatePartnerReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\Segment as SegmentReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SegmentRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\AbstractReportRepository;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;

class DailyReportCreator
{
    use AggregatePartnerReportTrait;
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var ReportCreatorInterface
     */
    private $reportCreator;
    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;
    /**
     * @var RonAdSlotManagerInterface
     */
    private $ronAdSlotManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ObjectManager $om, ReportCreatorInterface $reportCreator,
        SegmentRepositoryInterface $segmentRepository, RonAdSlotManagerInterface $ronAdSlotManager
    )
    {
        $this->om = $om;
        $this->reportCreator = $reportCreator;
        $this->segmentRepository = $segmentRepository;
        $this->ronAdSlotManager = $ronAdSlotManager;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Create all reports and persist them
     *
     * @param PublisherInterface[] $publishers
     * @param AdNetworkInterface[] $adNetworks
     */
    public function createAndSave(array $publishers, array $adNetworks)
    {
        $this->logger->info('Getting platform report');
        $platformReport = $this->reportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $this->logger->info('Persisting platform report');
        $this->om->persist($platformReport);
        $this->logger->info('flushing then detaching platform report');
        $this->om->flush();
        unset($platformReport);
        $this->logger->info('Finished platform report');
        gc_collect_cycles();

        $this->createAdNetworkReports($adNetworks);

        $this->om->flush();
        unset($adNetworks);

        $this->logger->info('Finished all network reports');

        gc_collect_cycles();

        $this->createSegmentReports($autoFlush = false);

        $this->logger->info('Start Creating report for ron slots');

        $this->createRonSlotReports($autoFlush = false);

        $this->logger->info('finished ron slot reports now start flushing');

        $this->om->flush();


        $this->logger->info('finished all segment and ron slot reports');

    }

    public function createReportsForPublishers(array $publishers, array $adNetworks)
    {
        $this->createAccountReports($publishers);

        $this->createAdNetworkReports($adNetworks);

        $this->om->flush();

    }

    public function createPlatformReport(DateTime $reportDate)
    {
        $report = new PlatformReport();
        $report->setDate($reportDate);

        $accountReportRepository = $this->om->getRepository(AccountReport::class);
        if (!$accountReportRepository instanceof AccountReportRepositoryInterface) {
            throw new \Exception('Invalid repository');
        }
        /**
         * @var AccountReportRepositoryInterface|AbstractReportRepository $accountReportRepository
         */

        $platformCounts = $accountReportRepository->getAggregatedReportsByDateRange($reportDate, $reportDate);


        $accountReports = $accountReportRepository->getReportsByDateRange($reportDate, $reportDate);
        foreach ($accountReports as $accountReport) {
            /**
             * @var AccountReport $accountReport
             */
            if ($accountReport->getSuperReport() != null) {
                throw new \Exception('Something went wrong. Platform report has not been created but the account report already has reference');
            }

            $accountReport->setSuperReport($report);
            $report->addSubReport($accountReport);
        }

        $report->parseData($platformCounts)
            ->setFillRate()
            ->setThresholdBilledAmount($chainToSubReports = false) // we don't need to calculate for sub reports
        ;

        $this->om->persist($report);
        $this->om->flush();
    }

    protected function createAccountReports(array $publishers)
    {
        $createdReports = [];
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            $accountReport = $this->reportCreator->getReport(
                new AccountReportType($publisher)
            );
            /**
             * @var AccountReport $accountReport
             */
            $this->om->persist($accountReport);
            $createdReports[] = $accountReport;
            unset($accountReport);
        }

        $this->flushThenDetach($createdReports);
        unset($createdReports);
    }

    /**
     * @param AdNetworkInterface[] $adNetworks
     */
    protected function createAdNetworkReports(array $adNetworks)
    {
        foreach($adNetworks as $adNetwork) {
            if (!$adNetwork instanceof AdNetworkInterface) {
                continue;
            }

            $this->logger->info(sprintf('creating report for ad network %d', $adNetwork->getId()));
            /**
            * @var AdNetworkReport $adNetworkReport
            */
            $adNetworkReport = $this->reportCreator->getReport(
               new AdNetworkReportType($adNetwork)
            );
            $this->logger->info('Persisting report for ad network');
            $this->om->persist($adNetworkReport);
            $this->logger->info(sprintf('Finished report for ad network %d', $adNetwork->getId()));

            unset($adNetworkReport);
        }
    }

    public function createSegmentReports($autoFlush = true)
    {
        $this->logger->info('Getting all segments');
        $segments = $this->segmentRepository->findAll();

        foreach($segments as $segment) {
            if (!$segment instanceof SegmentInterface) {
                continue;
            }

            $this->logger->info(sprintf('Getting report for segment %d', $segment->getId()));

            $segmentReport = $this->reportCreator->getReport(
                new SegmentReportType($segment)
            );

            $this->logger->info(sprintf('Persisting segment %d', $segment->getId()));

            $this->om->persist($segmentReport);
            unset($segmentReport);
        }

        if ($autoFlush === true) {
            $this->om->flush();
        }

        $this->logger->info('finished creating segment reports');
    }

    public function createRonSlotReports($autoFlush = true)
    {
        $ronAdSlotsWithoutSegment = $this->ronAdSlotManager->all();

        foreach($ronAdSlotsWithoutSegment as $ronAdSlot) {
            if (!$ronAdSlot instanceof RonAdSlotInterface) {
                continue;
            }

            $lib = $ronAdSlot->getLibraryAdSlot();
            if (!$lib instanceof ReportableLibraryAdSlotInterface) {
                continue;
            }

            $this->logger->info(sprintf('Getting report for ron %d', $ronAdSlot->getId()));

            $ronAdSlotReport = $this->reportCreator->getReport(
                new RonAdSlotReportType($ronAdSlot)
            );

            $this->logger->info(sprintf('Persisting report for ron %d', $ronAdSlot->getId()));


            $this->om->persist($ronAdSlotReport);
            unset($ronAdSlotReport);
        }

        if ($autoFlush === true) {
            $this->om->flush();
        }

    }

    protected function flushThenDetach($entities)
    {
        $this->om->flush();
        $this->detach($entities);
    }

    protected function detach($entities)
    {
        $myEntities = is_array($entities) ? $entities : [$entities];
        foreach ($myEntities as $entity) {
            $this->om->detach($entity);
        }
    }

    /**
     * @return DateTime
     */
    public function getReportDate()
    {
        return $this->reportCreator->getDate();
    }

    /**
     * @param DateTime $reportDate
     * @return $this
     */
    public function setReportDate(DateTime $reportDate)
    {
        $this->reportCreator->setDate($reportDate);

        return $this;
    }


}