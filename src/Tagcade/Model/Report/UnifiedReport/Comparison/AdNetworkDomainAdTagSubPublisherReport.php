<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportInterface as PerformanceAdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportInterface as UnifiedAdNetworkDomainAdTagSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AdNetworkDomainAdTagSubPublisherReport extends AbstractReport implements AdNetworkDomainAdTagSubPublisherReportInterface, ReportInterface
{
    const ALL_AD_NETWORK = 'all.ad_network';

    protected $id;

    protected $date;

    /** @var string */
    protected $domain;

    /** @var string */
    protected $partnerTagId;

    /** @var AdNetworkInterface */
    protected $adNetwork;

    /** @var SubPublisherInterface */
    protected $subPublisher;

    /** @var PerformanceAdNetworkDomainAdTagSubPublisherReportInterface */
    protected $performanceAdNetworkDomainAdTagSubPublisherReport;

    /** @var UnifiedAdNetworkDomainAdTagSubPublisherReportInterface */
    protected $unifiedAdNetworkDomainAdTagSubPublisherReport;

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
    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return $this->subPublisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPerformanceAdNetworkDomainAdTagSubPublisherReport()
    {
        return $this->performanceAdNetworkDomainAdTagSubPublisherReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceAdNetworkDomainAdTagSubPublisherReport($performanceAdNetworkDomainAdTagSubPublisherReport)
    {
        $this->performanceAdNetworkDomainAdTagSubPublisherReport = $performanceAdNetworkDomainAdTagSubPublisherReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedAdNetworkDomainAdTagSubPublisherReport()
    {
        return $this->unifiedAdNetworkDomainAdTagSubPublisherReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedAdNetworkDomainAdTagSubPublisherReport($unifiedAdNetworkAdTagSubPublisherReport)
    {
        $this->unifiedAdNetworkDomainAdTagSubPublisherReport = $unifiedAdNetworkAdTagSubPublisherReport;
        return $this;
    }

    /**
     * @return float
     */
    protected function calculateFillRate()
    {
        // TODO: Implement calculateFillRate() method.
    }

    public function getName()
    {
        return $this->partnerTagId;
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getImpressions();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getPassbacks();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getPassbacks();
        }

        return 0;
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getEstCpm();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedAdNetworkDomainAdTagSubPublisherReport instanceof unifiedAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagSubPublisherReport->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAdNetworkDomainAdTagSubPublisherReport instanceof PerformanceAdNetworkDomainAdTagSubPublisherReportInterface) {
            return $this->performanceAdNetworkDomainAdTagSubPublisherReport->getEstRevenue();
        }

        return 0;
    }
}