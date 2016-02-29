<?php

namespace Tagcade\Model\Report\RtbReport\ReportType\Hierarchy;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReportInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\AbstractReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

class AdSlot extends AbstractReportType implements ReportTypeInterface
{
    const REPORT_TYPE = 'adSlot';

    /**
     * @var ReportableAdSlotInterface
     */
    private $adSlot;

    public function __construct(ReportableAdSlotInterface $adSlot)
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
        /** @var SiteInterface $site */
        $site =  $this->adSlot->getSite();
        return array(
            'id' => $site->getId(),
            'domain' => $site->getDomain()
        );
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdSlotReportInterface;
    }
}