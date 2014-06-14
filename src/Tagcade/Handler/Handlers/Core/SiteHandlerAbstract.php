<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\SiteInterface;

abstract class SiteHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SiteInterface::class);
    }
}