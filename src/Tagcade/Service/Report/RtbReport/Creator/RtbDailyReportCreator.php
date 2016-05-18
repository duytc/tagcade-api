<?php

namespace Tagcade\Service\Report\RtbReport\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Model\Core\ReportableLibraryAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Platform as PlatformReportType;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\User\Role\PublisherInterface;

class RtbDailyReportCreator
{
    /** @var ObjectManager */
    private $om;

    /** @var RtbReportCreatorInterface */
    private $rtbReportCreator;

    /**
     * @var RonAdSlotManagerInterface
     */
    private $ronAdSlotManager;

    public function __construct(ObjectManager $om, RtbReportCreatorInterface $rtbReportCreator, RonAdSlotManagerInterface $ronAdSlotManager)
    {
        $this->om = $om;
        $this->rtbReportCreator = $rtbReportCreator;
        $this->ronAdSlotManager = $ronAdSlotManager;
    }

    /**
     * Create all reports and persist them
     * @param array|PublisherInterface $publishers
     */
    public function createAndSave(array $publishers)
    {
        /* platform reports */
        $platformReport = $this->rtbReportCreator->getReport(
            new PlatformReportType($publishers)
        );

        $this->om->persist($platformReport);

        $this->flushThenDetach($platformReport);
        unset($platformReport);

        /* ron ad slot reports, ron ad slot segment reports,  */
        $ronAdSlots = $this->ronAdSlotManager->all();
        foreach ($ronAdSlots as $ronAdSlot) {
            // sure RonAdSlot instance
            if (!$ronAdSlot instanceof RonAdSlotInterface) {
                continue;
            }

            // sure reportable RonAdSlot (Display, Native), note: not required has deployed ad slot!!!
            $libSlot = $ronAdSlot->getLibraryAdSlot();
            if (!$libSlot instanceof ReportableLibraryAdSlotInterface) {
                continue;
            }

            // create ron ad slot report
            $ronAdSlotReport = $this->rtbReportCreator->getReport(
                new RonAdSLotReportType($ronAdSlot)
            );

            $this->om->persist($ronAdSlotReport);

            // also, create ron ad slot segment report (notice: same table for ron ad slot report!!!, difference from performance report)
            $segments = $ronAdSlot->getSegments();
            foreach($segments as $segment) {
                $ronAdSlotSegmentReport = $this->rtbReportCreator->getReport(
                    new RonAdSLotReportType($ronAdSlot, $segment)
                );

                $this->om->persist($ronAdSlotSegmentReport);
            }
        }

        $this->om->flush();
    }

    protected function flushThenDetach($entities)
    {
        $this->om->flush();
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
        return $this->rtbReportCreator->getDate();
    }

    /**
     * @param DateTime $reportDate
     * @return $this
     */
    public function setReportDate(DateTime $reportDate)
    {
        $this->rtbReportCreator->setDate($reportDate);

        return $this;
    }
}