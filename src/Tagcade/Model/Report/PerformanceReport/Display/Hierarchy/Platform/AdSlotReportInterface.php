<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AdSlotReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface AdSlotReportInterface extends BillableInterface, CalculatedReportInterface, AdSlotReportDataInterface, SuperReportInterface, SubReportInterface
{
    /**
     * @return float
     */
    public function getCustomRate();

    /**
     * @param float $customRate
     * @return self
     */
    public function setCustomRate($customRate);

    /**
     * @return ReportableAdSlotInterface
     */
    public function getAdSlot();

    /**
     * @return int|null
     */
    public function getAdSlotId();

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return self
     */
    public function setAdSlot(BaseAdSlotInterface $adSlot);

    /**
     * @return int
     */
    public function getRefreshedSlotOpportunities();

    /**
     * @param int $refreshedSlotOpportunities
     * @return self
     */
    public function setRefreshedSlotOpportunities($refreshedSlotOpportunities);
}