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
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("LibraryDisplayAdSlot")
 */
class LibraryDisplayAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad slots
     *
     * @Rest\View(
     *      serializerGroups={"librarydisplayadslot.summary" , "slotlib.detail", "user.summary"}
     * )
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"librarydisplayadslot.detail" , "slotlib.detail",  "user.summary"}
     * )
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
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
     * @return LibraryDisplayAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
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
             * @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot;
             */
            $libraryDisplayAdSlot = $this->getOr404($id);
            $referencingSlots = $libraryDisplayAdSlot->getDisplayAdSlots()->toArray();
            if (count($referencingSlots) > 0) {
                throw new BadRequestHttpException('There are some slots still referencing to this library');
            }

        }

        return $this->patch($request, $id);
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
        /** @var LibraryDisplayAdSlotInterface $libraryAdSlot */
        $libraryAdSlot = $this->one($id);

        $adSlot = current($libraryAdSlot->getDisplayAdSlots()->toArray());

        if (!$adSlot instanceof DisplayAdSlotInterface) {
            throw new NotFoundHttpException('not found any slot in this library');
        }

        $newAdTagOrderIds = $request->request->get('ids');

        if (!$newAdTagOrderIds) {
            throw new BadRequestHttpException("Ad tagIds parameter is required");
        }

        $result = array_values(
            $this->get('tagcade_app.service.core.ad_tag.ad_tag_position_editor')
                ->setAdTagPositionForAdSlot($adSlot, $newAdTagOrderIds)
        );

        return $result;
    }

    /**
     * Update the position of all ad tags in an ad slot
     *
     * @Rest\POST("/librarydisplayadslots/{id}/adtag", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagAction(Request $request, $id)
    {
        /** @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot */
        $libraryDisplayAdSlot = $this->one($id);

        /** @var DisplayAdSLotInterface[] $referencedAdSlots */
        $referencedAdSlots = $libraryDisplayAdSlot->getDisplayAdSlots()->toArray();

        if(null == $referencedAdSlots ||  count($referencedAdSlots) < 1)
        {
            return $this->view("Can not add an AdTag to an orphan AdSlot Library", Codes::HTTP_BAD_REQUEST);
        }

        $adSlot = $referencedAdSlots[0];
        // set AdSlot
        $request->request->add(array('adSlot' => $adSlot->getId()));
        unset($adSlot);
        unset($referencedAdSlots);

        // move the creating AdTag to Library
        $libraryAdTag = $request->request->get('libraryAdTag');
        $libraryAdTag['visible'] = true;
        $request->request->set('libraryAdTag', $libraryAdTag);
        unset($adTagLib);

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
     * @return DisplayAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryDisplayAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getDisplayAdSlots();
    }


    /**
     * Get those AdTags which belong to the given AdSlot Library
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
        /** @var LibraryDisplayAdSlotInterface $entity */
        $entity = $this->one($id);
        $adSlots = $entity->getDisplayAdSlots();

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
        return 'librarydisplayadslot';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_librarydisplayadslot';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_ad_slot');
    }
}