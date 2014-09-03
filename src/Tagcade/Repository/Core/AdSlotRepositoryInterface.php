<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\SiteInterface;

interface AdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);
}