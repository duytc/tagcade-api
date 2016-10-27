<?php

namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\CalculateWeightedValueTrait;
use Tagcade\Model\Report\VideoReport\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\SuperReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\Fields\VideoWaterfallTagTrait;

/**
 * A calculated report in the platform Reports contains sub reports
 *
 * i.e an ad slot report contains many ad tag reports
 *
 * These sub reports are used to generated the values for this report
 */
abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements CalculatedReportInterface, SuperReportInterface
{
    use VideoWaterfallTagTrait;
    use CalculateWeightedValueTrait;

    protected function doCalculateFields()
    {
        $this->adTagRequests = 0;
        $this->adTagBids = 0;
        $this->adTagErrors = 0;
        $this->billedAmount = 0;
        parent::doCalculateFields();
    }

    protected function postCalculateFields()
    {
        $this->setWeightedBilledRate();
    }

    public function setThresholdBilledAmount($chainToSubReports = true)
    {
        $this->billedAmount = 0;
        foreach ($this->subReports as $subReport) {
            if ($chainToSubReports === true && $subReport instanceof AbstractCalculatedReport) {
                $subReport->setThresholdBilledAmount(); // chain the calls to setCalculatedFields
            }

            $this->addBilledAmount($subReport->getBilledAmount());

            unset($subReport);
        }

        $this->setWeightedBilledRate();
    }

    protected function setWeightedBilledRate()
    {
        $weightedCpmRate = $this->calculateWeightedValue($this->getSubReports(), 'billedRate', 'billedAmount');
        $this->setBilledRate($weightedCpmRate);
    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        if (!$subReport instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expected a CalculatedReportInterface');
        }

        $this->addAdTagRequests($subReport->getAdTagRequests());
        $this->addAdTagBids($subReport->getAdTagBids());
        $this->addAdTagErrors($subReport->getAdTagErrors());
        $this->addBilledAmount($subReport->getBilledAmount());

        parent::aggregateSubReport($subReport);

    }

    protected function addAdTagRequests($adTagRequests)
    {
        $this->adTagRequests += (int)$adTagRequests;
    }

    protected function addAdTagBids($adTagBids)
    {
        $this->adTagBids += (int)$adTagBids;
    }

    protected function addAdTagErrors($adTagErrors)
    {
        $this->adTagErrors += (int)$adTagErrors;
    }

    protected function addBilledAmount($billedAmount)
    {
        $this->billedAmount += (float)$billedAmount;
    }

    protected function calculateFillRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('request must be defined to calculate fill rates');
        }

        return $this->getPercentage($this->getImpressions(), $this->getRequests());
    }

    protected function calculateBidRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('requests must be defined to calculate bid rates');
        }

        return $this->getPercentage($this->getBids(), $this->getRequests());
    }

    protected function calculateErrorRate()
    {
        if ($this->getBids() === null) {
            throw new RuntimeException('bids must be defined to calculate error rates');
        }

        return $this->getPercentage($this->getErrors(), $this->getBids());
    }
}