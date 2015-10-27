<?php

namespace Tagcade\Repository\Core;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class RonAdSlotSegmentRepository extends EntityRepository implements RonAdSlotSegmentRepositoryInterface
{
}