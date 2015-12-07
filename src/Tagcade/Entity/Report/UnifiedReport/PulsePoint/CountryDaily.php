<?php

namespace Tagcade\Entity\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\PulsePoint\CountryDaily as CountryDailyModel;

class CountryDaily extends CountryDailyModel
{
    protected $id;
    protected $publisherId;
    protected $day;
    protected $tagId;
    protected $adTagName;
    protected $adTagGroupId;
    protected $adTagGroupName;
    protected $country;
    protected $countryName;
    protected $paidImpressions;
    protected $allImpressions;
    protected $pubPayout;
    protected $fillRate;
    protected $cpm;
}