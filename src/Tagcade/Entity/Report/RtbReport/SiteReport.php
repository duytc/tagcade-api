<?php


namespace Tagcade\Entity\Report\RtbReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\SiteReport as SiteReportModel;

class SiteReport extends SiteReportModel
{
    protected $id;
    protected $site;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;
    protected $subReports;
    protected $superReport;
}