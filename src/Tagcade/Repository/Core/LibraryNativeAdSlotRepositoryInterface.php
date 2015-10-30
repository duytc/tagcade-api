<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface LibraryNativeAdSlotRepositoryInterface extends ObjectRepository
{
    public function getLibraryNativeAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getLibraryNativeAdSlotsUnusedInRonForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}