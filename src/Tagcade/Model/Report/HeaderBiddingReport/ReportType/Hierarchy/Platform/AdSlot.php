<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;

class AdSlot implements ReportTypeInterface
{
    /**
     * @var BaseAdSlotInterface
     */
    private $adSlot;

    public function  __construct(BaseAdSlotInterface $adSlot)
    {
        $this->adSlot = $adSlot;
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
        return $this->adSlot->getSite();
    }

    public function getSiteId()
    {
        return $this->adSlot->getSite()->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }
}