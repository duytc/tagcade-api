<?php

namespace Tagcade\Handler\Handlers\Core;

use Doctrine\ORM\EntityManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class AdSlotHandlerAbstract extends RoleHandlerAbstract
{
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