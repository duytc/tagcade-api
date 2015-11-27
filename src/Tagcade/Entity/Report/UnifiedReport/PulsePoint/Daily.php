<?php

namespace Tagcade\Entity\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\Daily as DailyModel;

class Daily extends DailyModel
{
    protected $id;
    protected $publisherId;
    protected $date;
    protected $size;
    protected $revenue;
    protected $fillRate;
    protected $paidImps;
    protected $backupImpression;
    protected $totalImps;
    protected $avgCpm;
}