<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Entity\Report\RtbReport\AdSlotReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorAbstract;

class RtbAdSlotSnapshot extends RtbSnapshotCreatorAbstract implements RtbAdSlotSnapshotInterface
{
    /**
     * @inheritdoc
     */
    public function doCreateReport(AdSlotReportType $reportType)
    {
        $report = new AdSlotReport();

        $adSlot = $reportType->getAdSlot();

        $report
            ->setAdSlot($adSlot)
            ->setName($adSlot->getName())
            ->setDate($this->getDate());

        /*
         * NOTICE: when using DailyReportCreator tools for yesterday, we want an exactly report and allow executing takes more time.
         * So that use supportMGet = false to get SEQUENTIALLY all cache records from all redis server instances.
         *
         * But for today report view from web, high accuracy report is not very important, hence we use supportMGet = true.
         */
        $supportMGet = !($this->getDate() < new \DateTime('today'));

        $adSlotReportCounts[] = $this->eventCounter->getRtbAdSlotReport($adSlot->getId(), $supportMGet);

        $this->parseRawReportData($report, $adSlotReportCounts);

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AdSlotReport) {
            throw new InvalidArgumentException('Expect instance AdSlotReport');
        }

        $report
            ->setOpportunities($data[self::RESULT_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::RESULT_KEY_IMPRESSION])
            ->setEarnedAmount($data[self::RESULT_KEY_PRICE])
            ->setFillRate();
    }
}