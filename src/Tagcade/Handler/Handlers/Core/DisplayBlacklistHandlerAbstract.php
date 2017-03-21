<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class DisplayBlacklistHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return DisplayBlacklistManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}