<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BlacklistRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param $suffixKey
     * @return null|BlacklistInterface
     */
    public function findBlacklistBySuffixKey($suffixKey);

    /**
     * @param array $builtinBlacklist
     * @return mixed
     */
    public function setBuiltinBlacklist(array $builtinBlacklist);
}