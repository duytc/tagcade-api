<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
//use Symfony\Component\HttpFoundation\Request;

class AccountController extends FOSRestController implements ClassResourceInterface
{
    public function getAction()
    {
        $user = $this->getUser();

        $view = $this->view($user);
        $view->getSerializationContext()->setGroups(['summary']);

        return $this->handleView($view);
    }

//    protected function patch(Request $request)
//    {
//
//    }
}