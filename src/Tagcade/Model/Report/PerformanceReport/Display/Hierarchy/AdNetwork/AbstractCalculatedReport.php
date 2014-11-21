<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Exception\RuntimeException;

abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements ReportInterface
{
    /**
     * @inheritdoc
     */
    protected function setFillRate()
    {
        $this->fillRate = $this->getPercentage($this->getImpressions(), $this->getTotalOpportunities());

        return $this;
    }
}