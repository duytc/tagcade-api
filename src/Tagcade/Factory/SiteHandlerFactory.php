<?php

namespace Tagcade\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Handler\SiteHandlerInterface;
use Tagcade\Handler\AdminSiteHandler;
use Tagcade\Handler\PublisherSiteHandler;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\RoleInterface;

class SiteHandlerFactory
{
    /**
     * @param ObjectManager $om
     * @param $entityClass
     * @param FormFactoryInterface $formFactory
     * @param FormTypeInterface|string $formType
     * @param RoleInterface $role
     * @return SiteHandlerInterface
     * @throws InvalidArgumentException;
     */
    public static function getHandler(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $formType, RoleInterface $role)
    {
        if ($role instanceof AdminInterface) {
            return new AdminSiteHandler($om, $entityClass, $formFactory, $formType, $role);
        }

        if ($role instanceof PublisherInterface) {
            return new PublisherSiteHandler($om, $entityClass, $formFactory, $formType, $role);
        }

        throw new InvalidArgumentException('No SiteHandler for the given role');
    }
}