<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends FOSRestController
{
    /**
     * Get all users
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return array
     */
    public function getUsersAction()
    {
        return $this->getUserHandler()->all();
    }

    /**
     * Get single User.
     *
     * @ApiDoc(
     *  resource = true,
     *  description = "Gets a User for a given id",
     *  output = "Tagcade\Bundle\UserBundle\Entity\User",
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the user is not found"
     *  }
     * )
     *
     * @param int $id the site id
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User
     *
     * @throws NotFoundHttpException when site does not exist
     */
    public function getUserAction($id)
    {
        return $this->getOr404($id);
    }

    protected function getOr404($id)
    {
        if (!($user = $this->getUserHandler()->get($id))) {
            throw new NotFoundHttpException(sprintf("The user resource '%s' was not found or you do not have access", $id));
        }

        return $user;
    }

    protected function getUserHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.user');
    }
}
