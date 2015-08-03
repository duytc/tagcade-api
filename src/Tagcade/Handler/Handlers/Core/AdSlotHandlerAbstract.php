<?php

namespace Tagcade\Handler\Handlers\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Handler\CloneAdSlotTrait;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

abstract class AdSlotHandlerAbstract extends RoleHandlerAbstract
{
    use CloneAdSlotTrait;
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}