<?php

namespace Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReport as SiteReportModel;

class SiteReport extends SiteReportModel
{
    protected $site;
    protected $superReport;
    protected $subReports;
    protected $id;
    protected $date;
    protected $name;
    protected $billedAmount;
    protected $billedRate;
    protected $requests;
}