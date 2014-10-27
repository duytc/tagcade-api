<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;

class AdSlot implements ReportTypeInterface
{
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
}