<?php


namespace Tagcade\Service\Report\SourceReport\Selector\Grouper;


use Tagcade\Service\Report\SourceReport\Result\Group\ReportGroup;
use Tagcade\Service\Report\SourceReport\Result\ReportResultInterface;

interface ReportGrouperInterface
{
    /**
     * @param ReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportResultInterface $reportCollection);
}