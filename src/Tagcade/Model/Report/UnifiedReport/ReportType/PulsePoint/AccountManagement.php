<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType\PulsePoint;

use Tagcade\Model\Report\UnifiedReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountManagement implements ReportTypeInterface
{
    /**
     * @var PublisherInterface
     */
    private $publisher;
    /**
     * @var
     */
    private $tagId;

    public function __construct(PublisherInterface $publisher, $tagId)
    {
        $this->publisher = $publisher;
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