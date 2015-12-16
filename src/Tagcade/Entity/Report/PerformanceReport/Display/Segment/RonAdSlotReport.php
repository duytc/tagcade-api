<?php

namespace Tagcade\Entity\Report\PerformanceReport\Display\Segment;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReport as RonAdSlotReportModel;

class RonAdSlotReport extends RonAdSlotReportModel
{
    protected $id;
    protected $superReport;
    protected $subReports;
    protected $name;
    protected $date;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;
    protected $ronAdSlot;
    protected $segment;
    protected $customRate;
    protected $slotOpportunities;
    protected $billedAmount;
    protected $billedRate;
}