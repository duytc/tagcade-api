<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\User\Role\PublisherInterface;

class AdTagGroupDaily extends AbstractAccountManagement
{
    private $adTagGroup;

    public function __construct(PublisherInterface $publisher, $adTagGroup)
    {
        parent::__construct($publisher, null);
        $this->adTagGroup = $adTagGroup;
    }
}