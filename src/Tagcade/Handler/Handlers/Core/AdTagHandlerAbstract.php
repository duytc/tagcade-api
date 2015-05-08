<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class AdTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}