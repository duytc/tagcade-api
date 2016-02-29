<?php


namespace Tagcade\Entity\Report\RtbReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\PlatformReport as PlatformReportModel;

class PlatformReport extends PlatformReportModel
{
    protected $id;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;
    protected $subReports;
}