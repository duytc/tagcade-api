<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\VideoDemandPartnerManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class VideoDemandPartnerHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return VideoDemandPartnerManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}