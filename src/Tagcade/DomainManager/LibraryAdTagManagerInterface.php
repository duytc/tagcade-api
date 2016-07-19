<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdTagManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
    public function getLibraryAdTagsByHtml( $html, $limit = null, $offset = null);
}