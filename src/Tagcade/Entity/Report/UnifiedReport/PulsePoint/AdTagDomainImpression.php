<?php

namespace Tagcade\Entity\Report\UnifiedReport\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\PulsePoint\AdTagDomainImpression as AdTagDomainImpressionModel;

class AdTagDomainImpression extends AdTagDomainImpressionModel
{
    protected $id;
    protected $publisherId;
    protected $domain;
    protected $totalImps;
    protected $paidImps;
    protected $fillRate;
    protected $domainStatus;
    protected $adTag;
    protected $date;
}