<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;

class AdNetworkAdTag extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'partner.adNetworkAdTag';

    /** @var AdNetworkInterface */
    private $adNetwork;

    /** @var string */
    private $partnerTagId;

    public function __construct(AdNetworkInterface $adNetwork = null, $partnerTagId = null)
    {
        $this->adNetwork = $adNetwork;
        $this->partnerTagId = $partnerTagId;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    public function getAdNetworkId()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getId();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @param string $partnerTagId
     */
    public function setPartnerTagId($partnerTagId)
    {
        $this->partnerTagId = $partnerTagId;
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AdNetworkAdTagReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }
}