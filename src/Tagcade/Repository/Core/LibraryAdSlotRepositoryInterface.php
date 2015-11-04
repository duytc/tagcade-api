<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryAdSlotRepositoryInterface extends ObjectRepository {

    public function getLibraryAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryDisplayAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getAllLibraryAdSlotsUnusedInRon($publisherId = null, $limit = null, $offset = null);

    public function getAllLibraryAdSlotsUsedInRon($publisherId = null, $limit = null, $offset = null);
}