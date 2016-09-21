<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\VideoWaterfallTagItemManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class VideoWaterfallTagItemHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return VideoWaterfallTagItemManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}