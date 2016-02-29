<?php


namespace Tagcade\Entity\Report\RtbReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\AdSlotReport as AdSlotReportModel;

class AdSlotReport extends AdSlotReportModel
{
    protected $id;
    protected $adSlot;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;
    protected $superReport;
}