<?php

namespace Tagcade\Handler\Handlers\Core;


use Tagcade\DomainManager\LibraryDynamicAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibraryDynamicAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryDynamicAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
} 