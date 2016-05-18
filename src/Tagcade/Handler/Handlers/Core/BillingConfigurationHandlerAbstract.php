<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\BillingConfigurationManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class BillingConfigurationHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return BillingConfigurationManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}