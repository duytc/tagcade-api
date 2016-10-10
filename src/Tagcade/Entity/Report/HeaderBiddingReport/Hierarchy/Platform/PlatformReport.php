<?php

namespace Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReport as PlatformReportModel;

class PlatformReport extends PlatformReportModel
{
    protected $id;
    protected $date;
    protected $billedAmount;
    protected $billedRate;
    protected $subReports;
    protected $requests;
}