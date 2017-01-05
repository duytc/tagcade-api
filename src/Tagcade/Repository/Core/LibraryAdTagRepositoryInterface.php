<?php

namespace Tagcade\Repository\Core;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

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
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryAdTagsForLibraryAdSlot(BaseLibraryAdSlotInterface $libraryAdSlot, $limit = null, $offset = null);


    /**
     * query all AdTagLibrary object that belongs to a given PublisherInterface
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return QueryBuilder
     */
    public function getLibraryAdTagsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param $htmlValue
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getLibraryAdTagsByHtml($htmlValue,  $limit = null, $offset = null);


    public function getLibraryAdTagsWithPagination(UserRoleInterface $user, PagerParam $param);
}