<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\DisplayWhiteListManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class DisplayWhiteListHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return DisplayWhiteListManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}