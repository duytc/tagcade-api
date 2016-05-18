<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportInterface as PerformanceAccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\PublisherReportInterface as UnifiedAccountReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountReport extends AbstractReport implements AccountReportInterface, ReportInterface
{
    use CalculateRatiosTrait;
    use CalculateComparisonRatiosTrait;
    protected $id;

    protected $date;

    /** @var PublisherInterface */
    protected $publisher;
    /** @var PerformanceAccountReportInterface */
    protected $performanceAccountReport;
    /** @var UnifiedAccountReportInterface */
    protected $unifiedAccountReport;

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPerformanceAccountReport()
    {
        return $this->performanceAccountReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceAccountReport($performanceAccountReport)
    {
        $this->performanceAccountReport = $performanceAccountReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedAccountReport()
    {
        return $this->unifiedAccountReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedAccountReport($unifiedAccountReport)
    {
        $this->unifiedAccountReport = $unifiedAccountReport;
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
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getFirstName();
        }

        return '';
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getEstCpm();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getPassbacks();
        }

        return 0;
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedAccountReport instanceof UnifiedAccountReportInterface) {
            return $this->getUnifiedAccountReport()->getPassbacks();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceAccountReport instanceof PerformanceAccountReportInterface) {
            return $this->getPerformanceAccountReport()->getEstRevenue();
        }

        return 0;
    }
}