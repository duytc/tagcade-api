<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;

class UserController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all publisher
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User[]
     */
    public function cgetAction()
    {
        return $this->getHandler()->allPublishers();
    }

    /**
     * Get a single publisher for the given id
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        $this->checkPermission($id);

        return $this->one($id);
    }

    /**
     * Create a user from the submitted data
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true},
     *      {"name"="email", "dataType"="string", "required"=false},
     *      {"name"="plainPassword", "dataType"="string", "required"=true},
     *      {"name"="role", "dataType"="string", "required"=true, "default"="publisher", "description"="The role of the user, i.e publisher or admin"},
     *      {"name"="features", "dataType"="array", "required"=false, "description"="An array of enabled features for this user, not applicable to admins"},
     *      {"name"="enabled", "dataType"="boolean", "required"=false, "description"="Is this user account enabled or not?"},
     *  },
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        return $this->post($request);
    }

    /**
     * Update an existing user from the submitted data or create a new publisher
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when the resource is created",
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function putAction(Request $request, $id)
    {
        $this->checkPermission($id);

        return $this->put($request, $id);
    }

    /**
     * Update an existing user from the submitted data or create a new publisher at a specific location
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request the request object
     * @param int $id the resource id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when resource not exist
     */
    public function patchAction(Request $request, $id)
    {
        $this->checkPermission($id);

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing publisher
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteAction($id)
    {
        $this->checkPermission($id);

        return $this->delete($id);
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'admin_api_1_get_user';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.user');
    }

    /**
     * @param $publisherId
     * @return bool
     */
    protected function checkPermission($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->findPublisher($publisherId);

        if ($publisher === false) {
            throw new NotFoundHttpException('Publisher does not exist');
        }

        return true;
    }
}
