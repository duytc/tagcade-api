<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryAdTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibraryAdTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryAdTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}