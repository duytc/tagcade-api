<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class SubPublisherHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SubPublisherManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}