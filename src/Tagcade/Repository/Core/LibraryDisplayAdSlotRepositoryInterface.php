<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface LibraryDisplayAdSlotRepositoryInterface extends ObjectRepository
{
    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryDisplayAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAllLibraryDisplayAdSlotsForPublisherQuery(PublisherInterface $publisher);

    public function getLibraryAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param);
}