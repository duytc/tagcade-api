<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\AdSlotInterface;

/**
 * @Rest\RouteResource("Adslot")
 */
class AdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single adSlot for the given id
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return AdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a adSlot from the submitted data
     *
     * @ApiDoc(
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
     * Update an existing adSlot from the submitted data or create a new adSlot
     *
     * @ApiDoc(
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
     * Update an existing adSlot from the submitted data or create a new adSlot at a specific location
     *
     * @ApiDoc(
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
     * Delete an existing adSlot
     *
     * @ApiDoc(
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

    public function getAdtagsAction($id)
    {
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForAdSlot($adSlot);
    }

    /**
     * Update the position of all ad tags in an ad slot
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagsPositionsAction(Request $request, $id)
    {
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        $newAdTagOrderIds = $request->request->get('ids');

        if (!$newAdTagOrderIds) {
            throw new BadRequestHttpException("Ad tagIds parameter is required");
        }

        return array_values(
            $this->get('tagcade_app.service.core.ad_tag.ad_tag_position_editor')
            ->setAdTagPositionForAdSlot($adSlot, $newAdTagOrderIds)
        );
    }

    /**
     * @param int $id
     * @return View
     */
    public function getJstagAction($id)
    {
        /** @var AdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createDisplayAdTag($adSlot);
    }

    protected function getResourceName()
    {
        return 'adslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_adslot';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_slot');
    }
}
