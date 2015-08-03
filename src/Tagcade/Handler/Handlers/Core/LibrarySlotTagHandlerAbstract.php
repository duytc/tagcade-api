<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibrarySlotTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibrarySlotTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibrarySlotTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}