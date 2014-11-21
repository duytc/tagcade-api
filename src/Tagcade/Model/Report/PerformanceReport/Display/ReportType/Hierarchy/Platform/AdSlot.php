<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class AdSlot extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'platform.adSlot';

    /**
     * @var AdSlotInterface
     */
    private $adSlot;

    public function __construct(AdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
    }

    /**
     * @return AdSlotInterface
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

    /**
     * @inheritdoc
     */
    public function isValidReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }
}