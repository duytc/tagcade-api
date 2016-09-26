<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\WhiteListManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class WhiteListHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return WhiteListManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}