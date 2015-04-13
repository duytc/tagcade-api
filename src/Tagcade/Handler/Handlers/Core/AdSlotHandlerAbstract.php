<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class AdSlotHandlerAbstract extends RoleHandlerAbstract
{
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