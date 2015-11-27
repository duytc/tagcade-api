<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DomainImpression implements ReportTypeInterface
{
    protected $domain;
    /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(PublisherInterface $publisher, $domain)
    {
        $this->publisher = $publisher;
        $this->domain = $domain;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisherId()
    {
        return $this->publisher->getId();
    }
}