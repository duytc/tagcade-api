<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\ChannelManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class ChannelHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return ChannelManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }
}