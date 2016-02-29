<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Model\Report\RtbReport\AbstractCalculatedReport as BaseAbstractCalculatedReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\Fields\OpportunitiesTrait;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\SuperReportInterface;

/**
 * A calculated report in the platform Reports contains sub reports
 *
 * i.e an ad slot report contains many ad tag reports
 *
 * These sub reports are used to generated the values for this report
 */
abstract class AbstractCalculatedReport extends BaseAbstractCalculatedReport implements ReportInterface, SuperReportInterface
{
    use OpportunitiesTrait;

    protected function doCalculateFields()
    {
        $this->opportunities = 0;

        parent::doCalculateFields();
    }

    protected function postCalculateFields()
    {

    }

    protected function aggregateSubReport(ReportInterface $subReport)
    {
        $this->addOpportunities($subReport->getOpportunities());

        parent::aggregateSubReport($subReport);
    }

    protected function addOpportunities($opportunities)
    {
        $this->opportunities += $opportunities;
    }
}