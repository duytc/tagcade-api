<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\VideoPublisherManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class VideoPublisherHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return VideoPublisherManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}