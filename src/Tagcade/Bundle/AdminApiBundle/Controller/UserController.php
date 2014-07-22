<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Tagcade\Bundle\UserBundle\Entity\User;

class UserController extends FOSRestController
{
    public function getUsersAction()
    {
        return $this->container->get('tagcade_admin_api.handler.user')->all();
    }
}
