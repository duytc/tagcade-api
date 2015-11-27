<?php

namespace Tagcade\Entity\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\AccountManagement as AccountManagementModel;

class AccountManagement extends AccountManagementModel
{
    protected $id;
    protected $publisherId;
    protected $adTagGroup;
    protected $adTag;
    protected $adTagId;
    protected $status;
    protected $size;
    protected $askPrice;
    protected $revenue;
    protected $fillRate;
    protected $paidImps;
    protected $backupImpression;
    protected $totalImps;
    protected $avgCpm;
    protected $date;
}