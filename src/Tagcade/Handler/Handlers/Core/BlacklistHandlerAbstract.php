<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\BlacklistManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class BlacklistHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return BlacklistManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}