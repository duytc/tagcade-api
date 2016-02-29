<?php


namespace Tagcade\Model\Report\RtbReport\Hierarchy;


use Tagcade\Model\Report\RtbReport\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    protected function setDefaultName()
    {
        // do nothing, a name isn't needed for this report
    }
}