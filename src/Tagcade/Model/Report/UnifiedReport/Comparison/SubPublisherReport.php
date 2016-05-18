<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;


use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\CalculateRatiosTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportInterface as PerformanceSubPublisherReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\UnifiedReport\Publisher\SubPublisherReportInterface as UnifiedSubPublisherReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherReport extends AbstractReport implements SubPublisherReportInterface, ReportInterface
{
    use CalculateRatiosTrait;
    use CalculateComparisonRatiosTrait;

    protected $id;

    protected $date;

    /** @var SubPublisherInterface */
    protected $subPublisher;
    /** @var PerformanceSubPublisherReportInterface */
    protected $performanceSubPublisherReport;
    /** @var UnifiedSubPublisherReportInterface */
    protected $unifiedSubPublisherReport;

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
    public function getPerformanceSubPublisherReport()
    {
        return $this->performanceSubPublisherReport;
    }

    /**
     * @inheritdoc
     */
    public function setPerformanceSubPublisherReport($performanceSubPublisherReport)
    {
        $this->performanceSubPublisherReport = $performanceSubPublisherReport;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUnifiedSubPublisherReport()
    {
        return $this->unifiedSubPublisherReport;
    }

    /**
     * @inheritdoc
     */
    public function setUnifiedSubPublisherReport($unifiedSubPublisherReport)
    {
        $this->unifiedSubPublisherReport = $unifiedSubPublisherReport;
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
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getUser()->getUsername();
        }

        return '';
    }

    public function getPartnerEstCPM()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getEstCpm();
        }

        return 0;
    }

    public function getPartnerImpressions()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerEstRevenue()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getEstRevenue();
        }

        return 0;
    }

    public function getTagcadeTotalOpportunities()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadePassbacks()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getPassbacks();
        }

        return 0;
    }

    public function getPartnerFillRate()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getFillRate();
        }

        return 0;
    }

    public function getTagcadeFillRate()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getFillRate();
        }

        return 0;
    }

    public function getPartnerTotalOpportunities()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getTotalOpportunities();
        }

        return 0;
    }

    public function getTagcadeImpressions()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getImpressions();
        }

        return 0;
    }

    public function getPartnerPassbacks()
    {
        if ($this->unifiedSubPublisherReport instanceof UnifiedSubPublisherReportInterface) {
            return $this->getUnifiedSubPublisherReport()->getPassbacks();
        }

        return 0;
    }

    public function getTagcadeEstCPM()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getEstCpm();
        }

        return 0;
    }

    public function getTagcadeEstRevenue()
    {
        if ($this->performanceSubPublisherReport instanceof PerformanceSubPublisherReportInterface) {
            return $this->getPerformanceSubPublisherReport()->getEstRevenue();
        }

        return 0;
    }
}