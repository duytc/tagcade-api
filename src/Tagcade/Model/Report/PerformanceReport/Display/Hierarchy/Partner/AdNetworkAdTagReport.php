<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class AdNetworkAdTagReport extends AbstractCalculatedReport implements AdNetworkAdTagReportInterface
{
    use PartnerReportFields;

    protected $id;

    protected $date;
    protected $name;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;
    protected $partnerTagId;

    /** @var AdNetworkInterface */
    protected $adNetwork;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @inheritdoc
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @inheritdoc
     */
    public function setPartnerTagId($partnerTagId)
    {
        $this->partnerTagId = $partnerTagId;
        return $this;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function setAdNetwork(AdNetworkInterface $adNetwork)
    {
        $this->adNetwork = $adNetwork;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }



}