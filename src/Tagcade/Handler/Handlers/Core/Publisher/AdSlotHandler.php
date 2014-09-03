<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Tagcade\Exception\Handler\MethodCallNotAllowedForRoleException;
use Tagcade\Handler\Handlers\Core\AdSlotHandlerAbstract;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AdSlotHandler extends AdSlotHandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof PublisherInterface;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        throw new MethodCallNotAllowedForRoleException();
    }
}