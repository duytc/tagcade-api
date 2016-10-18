<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector\Grouper;

use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\Group\ReportGroup;

interface ReportGrouperInterface
{
    /**
     * @param ReportResultInterface $reportCollection
     * @return ReportGroup
     */
    public function groupReports(ReportResultInterface $reportCollection);
}
