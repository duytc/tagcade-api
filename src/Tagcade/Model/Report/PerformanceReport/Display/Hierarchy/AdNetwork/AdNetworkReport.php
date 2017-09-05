<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\CalculateNetworkOpportunityFillRateTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ImpressionBreakdownReportDataInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class AdNetworkReport extends AbstractCalculatedReport implements AdNetworkReportInterface, ImpressionBreakdownReportDataInterface
{
    use CalculateNetworkOpportunityFillRateTrait;

    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    /**
     * @return AdNetworkInterface|null
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @return int|null
     */
    public function getAdNetworkId()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getId();
        }

        return null;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return $this
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;

        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReport;
    }

    /**
     * @inheritdoc
     */
    protected function doCalculateFields()
    {
        parent::doCalculateFields();

        // difference calculate at network/network level
        $this->setNetworkOpportunityFillRate($this->calculateNetworkOpportunityFillRate($this->getAdOpportunities(), $this->getTotalOpportunities()));
    }

    protected function setDefaultName()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            $this->setName($this->adNetwork->getName());
        }
    }

    public function getName()
    {
        if ($this->adNetwork instanceof AdNetworkInterface) {
            return $this->adNetwork->getName();
        }

        return null;
    }
}