<?php


namespace Tagcade\Entity\Report\RtbReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\AccountReport as AccountReportModel;

class AccountReport extends AccountReportModel
{
    protected $id;
    protected $publisher;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;
    protected $subReports;
    protected $superReport;
}