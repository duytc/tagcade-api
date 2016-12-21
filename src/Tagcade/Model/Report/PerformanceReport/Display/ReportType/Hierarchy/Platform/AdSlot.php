<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class AdSlot extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.adSlot';

    /**
     * @var ReportableAdSlotInterface
     */
    private $adSlot;

    /**
     * @var array
     */
    private $site;

    public function     __construct(ReportableAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
        $this->site = array (
            'id' => $this->adSlot->getSite()->getId(),
            'name' => $this->adSlot->getSite()->getName(),
            'domain' => $this->adSlot->getSite()->getDomain(),
        );
    }

    /**
     * @return ReportableAdSlotInterface|NativeAdSlotInterface
     */
    public function getAdSlot()
    {
        return $this->adSlot;
    }

    /**
     * @return int|null
     */
    public function getAdSlotId()
    {
        return $this->adSlot->getId();
    }

    public function getAdSlotType()
    {
        return $this->adSlot->getType();
    }

    public function getSite()
    {
        return $this->site;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AdTagReportInterface;
    }
}