<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Tagcade\Handler\HandlerInterface;

/**
 * Class SubPublisherController
 * @package Tagcade\Bundle\UserSystem\SubPublisherBundle\Controller
 * @Rest\RouteResource("SubPublisher")
 */
class SubPublisherController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all sub publisher
     *
     * @ApiDoc(
     *  section = "admin|publisher",
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
        return $this->getHandler()->all();
    }

    /**
     * Get a single sub publisher for the given id
     * @Rest\View(
     *      serializerGroups={"user.summary", "subpublisher.summary"}
     * )
     * @ApiDoc(
     *  section = "subpublisher",
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
        return $this->one($id);
    }


    /**
     * Get full information a single sub publisher for the given id
     *
     * @Rest\Get("/fullInfo/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"user.summary", "subpublisher.detail", "subPublisherPartnerRevenue.detail", "subpublisher.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  section = "admin|publisher",
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
    public function getFullAction($id)
    {
        return $this->one($id);
    }


    /**
     * Create a user from the submitted data
     *
     * @ApiDoc(
     *  section = "admin|publisher",
     *  resource = true,
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true},
     *      {"name"="email", "dataType"="string", "required"=false},
     *      {"name"="plainPassword", "dataType"="string", "required"=true},
     *      {"name"="role", "dataType"="string", "required"=true, "default"="sub_publisher", "description"="The role of the user, i.e sub publisher"},
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
        //get Sites from request and override to request
        $request->request->set('sites', $this->getSites($request->request->get('sites', [])));

        return $this->post($request);
    }

    /**
     * Update an existing user from the submitted data or create a new sub publisher at a specific location
     *
     * @ApiDoc(
     *  section = "admin|publisher",
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
        //get Sites from request and override to request
        $request->request->set('sites', $this->getSites($request->request->get('sites', [])));

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
        return $this->delete($id);
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'subpublisher';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'sub_publisher_api_1_get_subpublisher';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade.handler.sub_publisher');
    }
}
