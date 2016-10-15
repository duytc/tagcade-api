<?php

namespace Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReport as AccountReportModel;

class AccountReport extends AccountReportModel
{
    protected $publisher;
    protected $superReport;
    protected $subReports;
    protected $billedAmount;
    protected $billedRate;
    protected $id;
    protected $date;
    protected $name;
    protected $requests;
}