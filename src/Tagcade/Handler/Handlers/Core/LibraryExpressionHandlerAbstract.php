<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\LibraryExpressionManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class LibraryExpressionHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return LibraryExpressionManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}
