<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

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