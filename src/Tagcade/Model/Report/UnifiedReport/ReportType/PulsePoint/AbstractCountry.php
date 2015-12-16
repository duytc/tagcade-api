<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;

abstract class AbstractCountry implements ReportTypeInterface
{
    /** @var PublisherInterface */
    private $publisher;
    private $country;
    private $tagId;

    public function __construct(PublisherInterface $publisher, $country, $tagId)
    {
        $this->publisher = $publisher;
        $this->country = $country;
        $this->tagId = $tagId;
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
}