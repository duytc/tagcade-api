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

    protected function doCalculateFields()
    {
        $totalOpportunities = $impressions = $passbacks = $estRevenue = 0;

        foreach($this->subReports as $subReport) {
            if (!$this->isValidSubReport($subReport)) {
                throw new RuntimeException('That sub report is not valid for this report');
            }

            /** @var ReportInterface $subReport */
            $subReport->setCalculatedFields(); // chain the calls to setCalculatedFields

            $totalOpportunities += $subReport->getTotalOpportunities();
            $impressions += $subReport->getImpressions();
            $passbacks += $subReport->getPassbacks();
            $estRevenue += $subReport->getEstRevenue();

            unset($subReport);
        }

        $this->setTotalOpportunities($totalOpportunities);
        $this->setImpressions($impressions);
        $this->setPassbacks($passbacks);
        $this->setEstRevenue($estRevenue);
    }
}