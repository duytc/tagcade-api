<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\CalculateRevenueTrait;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractReport;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\ImpressionBreakdownTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Core\AdTagInterface;

class AdTagReport extends AbstractReport implements AdTagReportInterface, ImpressionBreakdownReportDataInterface
{
    use SuperReportTrait;
    use CalculateRevenueTrait;
    use ImpressionBreakdownTrait;
    /**
     * @var AdTagInterface
     */
    protected $adTag;

    /**
     * @return AdTagInterface|null
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @return int|null
     */
    public function getAdTagId()
    {
        if ($this->adTag instanceof AdTagInterface) {
            return $this->adTag->getId();
        }

        return null;
    }

    /**
     * @param AdTagInterface $adTag
     * @return $this
     */
    public function setAdTag(AdTagInterface $adTag)
    {
        $this->adTag = $adTag;
        return $this;
    }

    public function setCalculatedFields()
    {
        $estRevenue = $this->calculateEstRevenue($this->getImpressions(), $this->getEstCpm());
        $this->setEstRevenue($estRevenue);

        parent::setCalculatedFields();
    }

    /**
     * @inheritdoc
     */
    protected function calculateFillRate()
    {
        if ($this->getTotalOpportunities() === null) {
            throw new RuntimeException('total opportunities must be defined to calculate ad tag fill rates');
        }

        return $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    protected function setDefaultName()
    {
        if ($this->adTag instanceof AdTagInterface) {
            $this->setName($this->adTag->getName());
        }
    }
}