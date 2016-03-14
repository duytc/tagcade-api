<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\SegmentManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;

abstract class SegmentHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return SegmentManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }



}