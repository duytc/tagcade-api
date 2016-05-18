<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportInterface as PerformanceAdNetworkAdTagReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagReportInterface as UnifiedAdNetworkAdTagReportInterface;

class AdNetworkAdTagReport extends AbstractReport implements AdNetworkAdTagReportInterface, ReportInterface
{
    const ALL_AD_NETWORK = 'all.ad_network';

    protected $id;

    protected $date;

    /** @var string */
    protected $partnerTagId;
    /** @var AdNetworkInterface */
    protected $adNetwork;
    /** @var PerformanceAdNetworkAdTagReportInterface */
    protected $performanceAdNetworkAdTagReport;
    /** @var UnifiedAdNetworkAdTagReportInterface */
    protected $unifiedAdNetworkAdTagReport;

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
    public function getPerformanceAdNetworkAdTagReport()
    {
        return $this->performanceAdNetworkAdTagReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceAdNetworkAdTagReport($performanceAdNetworkAdTagReport)
    {
        $this->performanceAdNetworkAdTagReport = $performanceAdNetworkAdTagReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedAdNetworkAdTagReport()
    {
        return $this->unifiedAdNetworkAdTagReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedAdNetworkAdTagReport($unifiedAdNetworkAdTagReport)
    {
        $this->unifiedAdNetworkAdTagReport = $unifiedAdNetworkAdTagReport;
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
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getTotalOpportunities();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getImpressions();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getPassbacks();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getPassbacks();
        }

        return 0;
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getEstCpm();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedAdNetworkAdTagReport instanceof UnifiedAdNetworkAdTagReportInterface) {
            return $this->unifiedAdNetworkAdTagReport->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAdNetworkAdTagReport instanceof PerformanceAdNetworkAdTagReportInterface) {
            return $this->performanceAdNetworkAdTagReport->getEstRevenue();
        }

        return 0;
    }
}