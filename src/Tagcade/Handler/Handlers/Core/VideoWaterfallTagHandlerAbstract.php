<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class VideoWaterfallTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return VideoWaterfallTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}