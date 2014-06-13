<?php

namespace Tagcade\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Tagcade\Model\User\Role\AdminInterface;

class AdminSiteHandler extends SiteHandler
{
    /**
     * @inheritdoc
     * @param AdminInterface $admin
     */
    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory, $formType, AdminInterface $admin)
    {
        parent::__construct($om, $entityClass, $formFactory, $formType, $admin);
    }

    public function setAdmin(AdminInterface $admin)
    {
        $this->setUserRole($admin);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = 0)
    {
        return $this->getRepository()->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }
}