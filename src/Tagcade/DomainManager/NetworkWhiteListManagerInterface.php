<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\User\Role\PublisherInterface;

interface NetworkWhiteListManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getByPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}