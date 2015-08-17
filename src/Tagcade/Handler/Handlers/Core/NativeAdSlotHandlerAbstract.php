<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\NativeAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class NativeAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return NativeAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}