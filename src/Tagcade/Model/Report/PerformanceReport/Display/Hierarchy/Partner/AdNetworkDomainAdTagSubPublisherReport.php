<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdNetworkDomainAdTagSubPublisherReport extends AbstractCalculatedReport implements AdNetworkDomainAdTagSubPublisherReportInterface
{
    use PartnerReportFields;

    protected $id;

    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    protected $partnerTagId;
    protected $domain;
    /** @var AdNetworkInterface */
    protected $adNetwork;
    /** @var SubPublisherInterface */
    protected $subPublisher;

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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @inheritdoc
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPartnerTagId()
    {
        return $this->partnerTagId;
    }

    /**
     * @param mixed $partnerTagId
     * @return self
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

    /**
     * @inheritdoc
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }
}