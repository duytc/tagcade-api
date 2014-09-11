<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\DomainManager\AdTagManagerInterface;

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