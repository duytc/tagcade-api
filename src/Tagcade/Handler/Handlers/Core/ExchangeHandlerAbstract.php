<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\ExchangeManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class ExchangeHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return ExchangeManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}