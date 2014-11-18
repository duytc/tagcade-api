<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\CPMRateDisplayManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class CPMRateDisplayHandlerAbstract  extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return CPMRateDisplayManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

} 