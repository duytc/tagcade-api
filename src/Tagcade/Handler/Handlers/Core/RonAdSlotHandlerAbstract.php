<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class RonAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return RonAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}