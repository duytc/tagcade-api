<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryAdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibraryAdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryAdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}
