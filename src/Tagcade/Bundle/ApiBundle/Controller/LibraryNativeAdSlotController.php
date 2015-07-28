<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("LibraryNativeAdSlot")
 */
class LibraryNativeAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.summary", "slotlib.detail", "user.summary"}
     * )
     *
     * Get all ad slots
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return LibraryNativeAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     *
     * Get a single adSlot for the given id
     *
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.detail", "slotlib.detail", "user.summary"}
     * )
     * @Rest\View(serializerEnableMaxDepthChecks=true)
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
     * @return LibraryNativeAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Update the position of all ad tags in an ad slot
     *
     * @Rest\POST("/librarynativeadslots/{id}/adtag", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagAction(Request $request, $id)
    {
        /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
        $libraryNativeAdSlot = $this->one($id);

        /** @var NativeAdSLotInterface[] $referencedAdSlots */
        $referencedAdSlots = $libraryNativeAdSlot->getNativeAdSlots()->toArray();

        if(null == $referencedAdSlots ||  count($referencedAdSlots) < 1)
        {
            return $this->view("Not found any associated slots to this library", Codes::HTTP_NOT_FOUND);
        }

        $adSlot = $referencedAdSlots[0];

        // set AdSlot
        $request->request->add(array('adSlot' => $adSlot->getId()));
        unset($adSlot);
        unset($referencedAdSlots);

        // move the creating AdTag to library
        $libraryAdTag = $request->request->get('libraryAdTag');
        $libraryAdTag['visible'] = true;
        $request->request->set('libraryAdTag', $libraryAdTag);
        unset($libraryAdTag);

        $this->get('tagcade_api.handler.ad_tag')->post($request->request->all());

        return $this->view(null, Codes::HTTP_CREATED);
    }


    /**
     * Create a adSlot library from the submitted data
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
        if(!array_key_exists('visible', $request->request->all()))
        {
            $request->request->add(array('visible' => true));
        }

        return $this->post($request);
    }

    /**
     * Update an existing nativeAdSlot from the submitted data or create a new nativeAdSlot at a specific location
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
        $params = $request->request->all();

        if (array_key_exists('visible', $params) && false == $params['visible']) {
            /**
             * @var LibraryNativeAdSlotInterface $libraryNativeAdSlot;
             */
            $libraryNativeAdSlot = $this->getOr404($id);
            $referencingSlots = $libraryNativeAdSlot->getNativeAdSlots()->toArray();
            if (count($referencingSlots) > 0) {
                throw new BadRequestHttpException('There are some slots still referencing to this library');
            }

        }

        return $this->patch($request, $id);
    }


    /**
     * Get those AdSlots which refer to the current AdSlot Library
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
     * @return NativeAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getNativeAdSlots();
    }


    /**
     * Get those AdTags which belong to the given AdSlot Library, also have been moved to AdTag Library
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
     * @return AdTagInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdtagsAction($id){
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);
        $adSlots = $entity->getNativeAdSlots();

        if(null == $adSlots || count($adSlots) < 1) return [];

        $adSlot = $adSlots[0];

        return $this->get('tagcade.domain_manager.ad_tag')->getAdTagsForAdSlot($adSlot);
    }

    /**
     * Delete an existing adTag library
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


    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'librarynativeadslot';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_librarynativeadslot';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_native_ad_slot');
    }
}