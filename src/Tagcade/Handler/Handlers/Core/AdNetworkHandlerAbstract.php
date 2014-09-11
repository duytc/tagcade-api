<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\DomainManager\AdNetworkManagerInterface;

abstract class AdNetworkHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdNetworkManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}