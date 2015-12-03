<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;

class AdTagGroupCountry extends AbstractCountry
{
    private $adTagGroup;

    public function __construct(PublisherInterface $publisher, $adTagGroup)
    {
        parent::__construct($publisher, $country = null, $tagId = null);
        $this->adTagGroup = $adTagGroup;
    }
}