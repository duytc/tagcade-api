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
use Tagcade\Model\Core\IvtPixelWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("IvtPixelWaterfallTag")
 */
class IvtPixelWaterfallTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     *
     * Get all Ivt Pixel Waterfall Tag
     * @Rest\View(
     *      serializerGroups={"ivtpixelWaterfallTag.summary", "ivtpixel.summary", "videoWaterfallTag.summary", "user.summary", "videoPublisher.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "Ivt Pixel Waterfall Tag",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return IvtPixelWaterfallTagInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $ivtPixelWaterfallTagManager = $this->get('tagcade.domain_manager.ivt_pixel_waterfall_tag');

        return ($role instanceof PublisherInterface)
            ? $ivtPixelWaterfallTagManager->getIvtPixelWaterfallTagsForPublisher($role)
            : $ivtPixelWaterfallTagManager->all();
    }

    /**
     * @Rest\Get("/ivtpixelwaterfalltags/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"ivtpixelWaterfallTag.summary", "ivtpixel.summary", "videoWaterfallTag.summary", "user.summary", "videoPublisher.summary"}
     * )
     * Get a single Ivt Pixel Waterfall Tag for the given id
     *
     * @ApiDoc(
     *  section = "Ivt Pixel Waterfall Tag",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return IvtPixelWaterfallTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     *
     * Create a Ivt Pixel Waterfall Tag from the submitted data
     *
     * @ApiDoc(
     *  section="Ivt Pixel Waterfall Tag",
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
     * Delete an existing Ivt Pixel Waterfall Tag
     *
     * @ApiDoc(
     *  section="Ivt Pixel Waterfall Tag",
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
        return 'ivtpixelwaterfalltag';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_ivtpixel_waterfall_tag';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ivt_pixel_waterfall_tag');
    }
}