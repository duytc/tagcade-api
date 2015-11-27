<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class Daily implements ReportTypeInterface
{
    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var \DateTime
     */
    private $date;

    public function __construct(PublisherInterface $publisher, \DateTime $date)
    {
        $this->publisher = $publisher;
        $this->date = $date;
    }

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        return $this->publisher->getId();
    }
}