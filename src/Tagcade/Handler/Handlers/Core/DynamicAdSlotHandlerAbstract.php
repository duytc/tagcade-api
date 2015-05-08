<?php

namespace Tagcade\Handler\Handlers\Core;


use Tagcade\DomainManager\DynamicAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class DynamicAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return DynamicAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
} 