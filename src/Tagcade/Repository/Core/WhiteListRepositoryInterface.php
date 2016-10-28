<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\WhiteListInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface WhiteListRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getWhiteListsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param $suffixKey
     * @return null|WhiteListInterface
     */
    public function findWhiteListBySuffixKey($suffixKey);

    /**
     * @param PublisherInterface $publisher
     * @param $name
     * @param null $orderBy
     * @param null $limit
     * @param null $offset
     * @return mixed
     */
    public function getWhiteListsByNameForPublisher(PublisherInterface $publisher, $name, $orderBy = null, $limit = null, $offset = null);
}