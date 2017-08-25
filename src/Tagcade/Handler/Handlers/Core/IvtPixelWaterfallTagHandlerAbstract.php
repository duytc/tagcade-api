<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;

abstract class IvtPixelWaterfallTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}