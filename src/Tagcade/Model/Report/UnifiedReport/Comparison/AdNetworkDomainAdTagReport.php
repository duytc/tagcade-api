<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReportInterface as PerformanceAdNetworkDomainAdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkDomainAdTagReportInterface as UnifiedAdNetworkDomainAdTagReportInterface;

class AdNetworkDomainAdTagReport extends AbstractReport implements AdNetworkDomainAdTagReportInterface, ReportInterface
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

    /** @var PerformanceAdNetworkDomainAdTagReportInterface */
    protected $performanceAdNetworkDomainAdTagReport;

    /** @var UnifiedAdNetworkDomainAdTagReportInterface */
    protected $unifiedAdNetworkDomainAdTagReport;

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
    public function getPerformanceAdNetworkDomainAdTagReport()
    {
        return $this->performanceAdNetworkDomainAdTagReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceAdNetworkDomainAdTagReport($performanceAdNetworkDomainAdTagReport)
    {
        $this->performanceAdNetworkDomainAdTagReport = $performanceAdNetworkDomainAdTagReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedAdNetworkDomainAdTagReport()
    {
        return $this->unifiedAdNetworkDomainAdTagReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedAdNetworkDomainAdTagReport($unifiedAdNetworkAdTagReport)
    {
        $this->unifiedAdNetworkDomainAdTagReport = $unifiedAdNetworkAdTagReport;
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
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getImpressions();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getPassbacks();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getPassbacks();
        }

        return 0;
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getEstCpm();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedAdNetworkDomainAdTagReport instanceof UnifiedAdNetworkDomainAdTagReportInterface) {
            return $this->unifiedAdNetworkDomainAdTagReport->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAdNetworkDomainAdTagReport instanceof PerformanceAdNetworkDomainAdTagReportInterface) {
            return $this->performanceAdNetworkDomainAdTagReport->getEstRevenue();
        }

        return 0;
    }
}