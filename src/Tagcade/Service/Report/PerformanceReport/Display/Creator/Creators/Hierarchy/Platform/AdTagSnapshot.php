<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdTagReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\EstCpmCalculatorInterface;

class AdTagSnapshot extends SnapshotCreatorAbstract implements AdTagInterface, SnapshotCreatorInterface
{
    /**
     * @var EstCpmCalculatorInterface
     */
    private $estCpmCalculator;

    function __construct(EstCpmCalculatorInterface $revenueCalculator)
    {
        $this->estCpmCalculator = $revenueCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdTagReportType $reportType)
    {
        $report = new AdTagReport();
        $adTag = $reportType->getAdTag();
        $isNativeAdSlot = $reportType->getAdTag()->getAdSlot() instanceof NativeAdSlotInterface;

        $report
            ->setAdTag($adTag)
            ->setName($adTag->getName())
            ->setDate($this->getDate())
            ->setPosition($adTag->getPosition())
        ;

        $adTagReportCount = $this->eventCounter->getAdTagReport($adTag->getId(), $isNativeAdSlot);

        $this->parseRawReportData($report, array($adTagReportCount));

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdTagReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof AdTagReport) {
            throw new InvalidArgumentException('Expect instance AdTagReport');
        }

        $report->setTotalOpportunities($data[self::CACHE_KEY_OPPORTUNITY])
            ->setImpressions($data[self::CACHE_KEY_IMPRESSION])
            ->setFirstOpportunities($data[self::CACHE_KEY_FIRST_OPPORTUNITY])
            ->setVerifiedImpressions($data[self::CACHE_KEY_VERIFIED_IMPRESSION])

            ->setUnverifiedImpressions($data[self::CACHE_KEY_UNVERIFIED_IMPRESSION])
            ->setPassbacks($data[self::CACHE_KEY_PASSBACK])
            ->setBlankImpressions($data[self::CACHE_KEY_BLANK_IMPRESSION])
            ->setVoidImpressions($data[self::CACHE_KEY_VOID_IMPRESSION])
            ->setClicks($data[self::CACHE_KEY_CLICK])
            ->setFillRate()
        ;

        // TODO latter
        $report->setEstCpm((float)0);
        $report->setEstRevenue((float)0);
    }
}