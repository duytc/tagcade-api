<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface LibraryNativeAdSlotRepositoryInterface extends ObjectRepository
{
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryNativeAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAllLibraryNativeAdSlotsForPublisherQuery(PublisherInterface $publisher);

    public function getLibraryAdSlotsWithPagination(UserRoleInterface $user, PagerParam $param);
}