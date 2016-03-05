<?php

namespace Tagcade\Bundle\UserBundle\Annotations\UserType;

use Doctrine\Common\Annotations\Annotation;

use Tagcade\Model\User\Role\SubPublisherInterface;

/**
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class SubPublisher implements UserTypeInterface
{
    public function getUserClass()
    {
        return SubPublisherInterface::class;
    }
}