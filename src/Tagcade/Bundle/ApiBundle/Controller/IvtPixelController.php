<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("IvtPixel")
 */
class IvtPixelController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ivt pixel
     * @Rest\View(serializerGroups={"ivtpixel.summary", "user.summary"})
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section = "Ivt Pixel",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return IvtPixelInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $ivtPixelManager = $this->get('tagcade.domain_manager.ivt_pixel');
        $ivtPixelRepository = $this->get('tagcade.repository.ivt_pixel');

        if ($request->query->get('page') > 0) {
            $qb = $ivtPixelRepository->getIvtPixelsForUserWithPagination($role, $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $ivtPixelManager->getIvtPixelsForPublisher($role)
            : $ivtPixelManager->all();
    }

    /**
     * @Rest\Get("/ivtpixels/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"ivtpixel.summary", "user.summary", "ivtpixelWaterfallTag.waterfallonly", "videoWaterfallTag.report"}
     * )
     * Get a single Ivt Pixel for the given id
     *
     * @ApiDoc(
     *  section = "Ivt Pixel",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return IvtPixelInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a IvtPixel from the submitted data
     *
     * @ApiDoc(
     *  section="Ivt Pixel",
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
        return $this->post($request);
    }

    /**
     * Update an existing IvtPixel from the submitted data or create a new IvtPixel
     *
     * @ApiDoc(
     *  section="IvtPixel",
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
        return $this->put($request, $id);
    }

    /**
     * Update an existing IvtPixel from the submitted data or create a new adTag at a specific location
     *
     * @ApiDoc(
     *  section="Ivt Pixel",
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
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing IvtPixel
     *
     * @ApiDoc(
     *  section="Ivt Pixel",
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
     * @return string
     */
    protected function getResourceName()
    {
        return 'ivtpixel';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_ivtpixel';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ivt_pixel');
    }
}