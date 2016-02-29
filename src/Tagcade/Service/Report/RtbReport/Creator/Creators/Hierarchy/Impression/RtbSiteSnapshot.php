<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Entity\Report\RtbReport\SiteReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReport;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Site as SiteReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorAbstract;

class RtbSiteSnapshot extends RtbSnapshotCreatorAbstract implements RtbSiteSnapshotInterface
{
    /**
     * @var RtbAdSlotSnapshot
     */
    private $rtbAdSlotSnapshotCreator;

    public function __construct(RtbAdSlotSnapshot $rtbAdSlotSnapshotCreator)
    {
        $this->rtbAdSlotSnapshotCreator = $rtbAdSlotSnapshotCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(SiteReportType $reportType)
    {
        $siteReport = new SiteReport();

        $site = $reportType->getSite();

        $siteReport
            ->setSite($site)
            ->setName($site->getName())
            ->setDate($this->getDate());

        $result = array(
            self::RESULT_KEY_SLOT_OPPORTUNITY => 0,
            self::RESULT_KEY_IMPRESSION => 0,
            self::RESULT_KEY_PRICE => 0,
        );

        $allAdSlots = $site->getReportableAdSlots();

        $this->rtbAdSlotSnapshotCreator->setEventCounter($this->eventCounter);

        foreach ($allAdSlots as $adSlot) {
            /** @var AdSlotReport $adSlotReport */
            $adSlotReport = $this->rtbAdSlotSnapshotCreator->createReport(new AdSlotReportType($adSlot));
            $result[self::RESULT_KEY_SLOT_OPPORTUNITY] += $adSlotReport->getOpportunities();
            $result[self::RESULT_KEY_IMPRESSION] += $adSlotReport->getImpressions();
            $result[self::RESULT_KEY_PRICE] += $adSlotReport->getEarnedAmount();

            $siteReport->addSubReport($adSlotReport->setSuperReport($siteReport));
        }

        $this->constructReportModel($siteReport, $result);

        return $siteReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof SiteReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof SiteReport) {
            throw new InvalidArgumentException('Expect instance SiteReport');
        }

        $report
            ->setOpportunities($data[self::RESULT_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::RESULT_KEY_IMPRESSION])
            ->setEarnedAmount($data[self::RESULT_KEY_PRICE])
            ->setFillRate();
    }
}