<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoPublisherManagerInterface extends ManagerInterface
{
    /**
     * get all VideoPublishers For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoPublisherInterface[]
     */
    public function getVideoPublishersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param $name
     * @param $publisherId
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function findByNameAndPublisherId($name, $publisherId, $limit = null, $offset = null);
}