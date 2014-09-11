<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\DomainManager\SiteManagerInterface;

abstract class SiteHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SiteManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}