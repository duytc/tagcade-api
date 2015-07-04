<?php

namespace Tagcade\Repository\Core;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class DisplayAdSlotRepository extends EntityRepository implements DisplayAdSlotRepositoryInterface
{

}