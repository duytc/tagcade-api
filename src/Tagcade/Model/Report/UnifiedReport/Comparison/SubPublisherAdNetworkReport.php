<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface as PerformanceSubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherNetworkReportInterface as UnifiedSubPublisherAdNetworkReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherAdNetworkReport extends AbstractReport implements SubPublisherAdNetworkReportInterface, ReportInterface
{
    const ALL_AD_NETWORK = 'all.ad_network';

    use CalculateRatiosTrait;
    use CalculateComparisonRatiosTrait;

    protected $id;

    protected $date;

    /** @var SubPublisherInterface */
    protected $subPublisher;
    /** @var AdNetworkInterface */
    protected $adNetwork;
    /** @var PerformanceSubPublisherAdNetworkReportInterface */
    protected $performanceSubPublisherAdNetworkReport;
    /** @var UnifiedSubPublisherAdNetworkReportInterface */
    protected $unifiedSubPublisherAdNetworkReport;

    /**
     * @inheritdoc
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
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
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
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
    public function getPerformanceSubPublisherAdNetworkReport()
    {
        return $this->performanceSubPublisherAdNetworkReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceSubPublisherAdNetworkReport($performanceSubPublisherAdNetworkReport)
    {
        $this->performanceSubPublisherAdNetworkReport = $performanceSubPublisherAdNetworkReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedSubPublisherAdNetworkReport()
    {
        return $this->unifiedSubPublisherAdNetworkReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedSubPublisherAdNetworkReport($unifiedSubPublisherAdNetworkReport)
    {
        $this->unifiedSubPublisherAdNetworkReport = $unifiedSubPublisherAdNetworkReport;
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

    public function getPartnerEstCPM()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getEstCpm();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getPassbacks();
        }

        return 0;
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedSubPublisherAdNetworkReport instanceof UnifiedSubPublisherAdNetworkReportInterface) {
            return $this->getUnifiedSubPublisherAdNetworkReport()->getPassbacks();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceSubPublisherAdNetworkReport instanceof PerformanceSubPublisherAdNetworkReportInterface) {
            return $this->getPerformanceSubPublisherAdNetworkReport()->getEstRevenue();
        }

        return 0;
    }
}