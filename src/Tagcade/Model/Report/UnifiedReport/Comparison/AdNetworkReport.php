<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportInterface as PerformanceAdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkReportInterface as UnifiedAdNetworkReportInterface;

class AdNetworkReport extends AbstractReport implements AdNetworkReportInterface, ReportInterface
{
    const ALL_AD_NETWORK = 'all.ad_network';
    protected $id;

    protected $date;

    /** @var AdNetworkInterface */
    protected $adNetwork;
    /** @var PerformanceAdNetworkReportInterface */
    protected $performanceAdNetworkReport;
    /** @var UnifiedAdNetworkReportInterface */
    protected $unifiedAdNetworkReport;

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
    public function getPerformanceAdNetworkReport()
    {
        return $this->performanceAdNetworkReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceAdNetworkReport($performanceAdNetworkReport)
    {
        $this->performanceAdNetworkReport = $performanceAdNetworkReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedAdNetworkReport()
    {
        return $this->unifiedAdNetworkReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedAdNetworkReport($unifiedAdNetworkReport)
    {
        $this->unifiedAdNetworkReport = $unifiedAdNetworkReport;
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
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getName();
        }

        return self::ALL_AD_NETWORK;
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getImpressions();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getPassbacks();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getPassbacks();
        }

        return 0;
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getEstCpm();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedAdNetworkReport instanceof UnifiedAdNetworkReportInterface) {
            return $this->unifiedAdNetworkReport->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAdNetworkReport instanceof PerformanceAdNetworkReportInterface) {
            return $this->performanceAdNetworkReport->getEstRevenue();
        }

        return 0;
    }
}