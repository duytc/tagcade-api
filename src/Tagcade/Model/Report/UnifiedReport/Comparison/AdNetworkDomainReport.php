<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportInterface as PerformanceAdNetworkDomainReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkSiteReportInterface as UnifiedNetworkSiteReportInterface;

class AdNetworkDomainReport extends AbstractReport implements AdNetworkDomainReportInterface, ReportInterface
{
    protected $id;

    protected $date;

    /** @var string */
    protected $domain;
    /** @var AdNetworkInterface */
    protected $adNetwork;
    /** @var PerformanceAdNetworkDomainReportInterface */
    protected $performanceAdNetworkDomainReport;
    /** @var UnifiedNetworkSiteReportInterface */
    protected $unifiedNetworkSiteReport;

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
     * @return PerformanceAdNetworkDomainReportInterface
     */
    public function getPerformanceAdNetworkDomainReport()
    {
        return $this->performanceAdNetworkDomainReport;
    }

    /**
     * @param PerformanceAdNetworkDomainReportInterface $performanceAdNetworkDomainReport
     * @return self
     */
    public function setPerformanceAdNetworkDomainReport($performanceAdNetworkDomainReport)
    {
        $this->performanceAdNetworkDomainReport = $performanceAdNetworkDomainReport;
        return $this;
    }

    /**
     * @return UnifiedNetworkSiteReportInterface
     */
    public function getUnifiedNetworkSiteReport()
    {
        return $this->unifiedNetworkSiteReport;
    }

    /**
     * @param UnifiedNetworkSiteReportInterface $unifiedNetworkSiteReport
     * @return self
     */
    public function setUnifiedNetworkSiteReport($unifiedNetworkSiteReport)
    {
        $this->unifiedNetworkSiteReport = $unifiedNetworkSiteReport;
        return $this;
    }


    /**
     * @return float
     */
    protected function calculateFillRate()
    {
        // TODO: Implement calculateFillRate() method.
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getImpressions();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getPassbacks();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getPassbacks();
        }

        return 0;
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getEstCpm();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedNetworkSiteReport instanceof UnifiedNetworkSiteReportInterface) {
            return $this->unifiedNetworkSiteReport->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAdNetworkDomainReport instanceof PerformanceAdNetworkDomainReportInterface) {
            return $this->performanceAdNetworkDomainReport->getEstRevenue();
        }

        return 0;
    }

    public function getName()
    {
        return $this->domain;
    }
}