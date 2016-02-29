<?php


namespace Tagcade\Entity\Report\RtbReport;
use Tagcade\Model\Report\RtbReport\Hierarchy\RonAdSlotReport as RonAdSlotReportModel;

class RonAdSlotReport extends RonAdSlotReportModel
{
    protected $id;
    protected $ronAdSlot;
    protected $segment;
    protected $name;
    protected $date;
    protected $opportunities;
    protected $impressions;
    protected $earnedAmount;
    protected $fillRate;
}