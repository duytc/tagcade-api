<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryNativeAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibraryNativeAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryNativeAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}
