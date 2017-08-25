<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface IvtPixelManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return IvtPixelInterface[]
     */
    public function getIvtPixelsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}