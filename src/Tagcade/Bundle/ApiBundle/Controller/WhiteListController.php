<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\Handlers\Core\BlacklistHandlerAbstract;
use Tagcade\Model\Core\WhiteListInterface;

/**
 * @Rest\RouteResource("WhiteList")
 */
class WhiteListController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all white list
     *
     * @Rest\View(
     *      serializerGroups={"whitelist.summary", "user.summary"}
     * )
     * @ApiDoc(
     *  section="White List",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return WhiteListInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single white list for the given id
     *
     * @Rest\View(
     *      serializerGroups={"whitelist.summary", "user.summary"}
     * )
     * @ApiDoc(
     *  section="White List",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return WhiteListInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }


    /**
     * Create a white list from the submitted data
     *
     * @ApiDoc(
     *  section="White List",
     *  resource = true,
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
        return $this->postAndReturnEntityData($request);
    }

    /**
     * Update an existing white list from the submitted data or create a new white list at a specific location
     *
     * @ApiDoc(
     *  resource = true,
     *  section="White List",
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
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing white list
     *
     * @ApiDoc(
     *  section="White List",
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

    protected function getResourceName()
    {
        return 'whitelist';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_whitelist';
    }

    /**
     * @return BlacklistHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.whitelist');
    }
}
