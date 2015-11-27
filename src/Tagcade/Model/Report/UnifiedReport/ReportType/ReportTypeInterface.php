<?php

namespace Tagcade\Model\Report\UnifiedReport\ReportType;


use Tagcade\Model\User\Role\PublisherInterface;

interface ReportTypeInterface
{
    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @return int
     */
    public function getPublisherId();
} 