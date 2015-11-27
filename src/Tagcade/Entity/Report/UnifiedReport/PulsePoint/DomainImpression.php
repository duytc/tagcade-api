<?php

namespace Tagcade\Entity\Report\UnifiedReport\PulsePoint;


use Tagcade\Model\Report\UnifiedReport\PulsePoint\DomainImpression as DomainImpressionModel;

class DomainImpression extends DomainImpressionModel
{
    protected $id;
    protected $publisherId;
    protected $domain;
    protected $totalImps;
    protected $paidImps;
    protected $fillRate;
    protected $domainStatus;
    protected $adTags;
    protected $date;
}