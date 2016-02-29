<?php

namespace Tagcade\Service\Report\RtbReport\Selector\Grouper;


use Tagcade\Service\Report\RtbReport\Selector\Result\Group\ReportGroup;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;

interface ReportGrouperInterface
{
    /**
     * @param ReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportResultInterface $reportCollection);
}
