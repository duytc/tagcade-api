<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class VideoDemandAdTagHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return VideoDemandAdTagManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}