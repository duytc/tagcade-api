<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdTagDomainImpression implements ReportTypeInterface
{
    protected $adTag;
    protected $domain;
    protected $date;
    /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(PublisherInterface $publisher, $adTag, $domain, $date)
    {
        $this->publisher = $publisher;
        $this->adTag = $adTag;
        $this->domain = $domain;
        $this->date = $date;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return mixed
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }
}