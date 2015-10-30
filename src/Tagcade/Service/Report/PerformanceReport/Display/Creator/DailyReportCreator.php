<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\Segment as SegmentReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Repository\Core\SegmentRepositoryInterface;

class DailyReportCreator
{
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

    public function __construct(ObjectManager $om, ReportCreatorInterface $reportCreator, SegmentRepositoryInterface $segmentRepository, RonAdSlotManagerInterface $ronAdSlotManager)
    {
        $this->om = $om;
        $this->reportCreator = $reportCreator;
        $this->segmentRepository = $segmentRepository;
        $this->ronAdSlotManager = $ronAdSlotManager;
    }

    /**
     * Create all reports and persist them
     *
     * @param PublisherInterface[] $publishers
     * @param AdNetworkInterface[] $adNetworks
     */
    public function createAndSave(array $publishers, array $adNetworks)
    {
        $createdReports = [];

        $platformReport = $this->reportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $this->om->persist($platformReport);
        $createdReports[] = $platformReport;

        foreach($adNetworks as $adNetwork) {
            $adNetworkReport = $this->reportCreator->getReport(
                new AdNetworkReportType($adNetwork)
            );

            $this->om->persist($adNetworkReport);
            $createdReports[] = $adNetworkReport;
        }

        foreach ($publishers as $publisher) {
            $segments = $this->segmentRepository->getSegmentsForPublisher($publisher);
            foreach($segments as $segment) {
                if ($segment instanceof SegmentInterface) {
                    $segmentReport = $this->reportCreator->getReport(
                        new SegmentReportType($segment)
                    );

                    $this->om->persist($segmentReport);
                    $createdReports[] = $segmentReport;
                }
            }
        }

        $ronAdSlotsWithoutSegment = $this->ronAdSlotManager->all();
        foreach($ronAdSlotsWithoutSegment as $ronAdSlot) {
            if (!$ronAdSlot instanceof RonAdSlotInterface) {
                continue;
            }

            $lib = $ronAdSlot->getLibraryAdSlot();
            if (!$lib instanceof ReportableLibraryAdSlotInterface) {
                continue;
            }

            $ronAdSlotReport = $this->reportCreator->getReport(
                new RonAdSlotReportType($ronAdSlot)
            );

            $this->om->persist($ronAdSlotReport);
            $createdReports[] = $ronAdSlotReport;
        }

        $this->om->flush();

        foreach($createdReports as $tempReport) {
            $this->om->detach($tempReport);
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