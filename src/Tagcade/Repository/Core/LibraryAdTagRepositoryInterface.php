<?php

namespace Tagcade\Repository\Core;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdTagRepositoryInterface extends ObjectRepository{

    /**
     * query all AdTagLibrary object that belongs to a given PublisherInterface
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getLibraryAdTagsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);


    /**
     * query all AdTagLibrary object that belongs to a given PublisherInterface
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder
     */
    public function getLibraryAdTagsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);
}