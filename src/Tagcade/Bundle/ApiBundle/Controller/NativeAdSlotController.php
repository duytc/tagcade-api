<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\Handler\Handlers\Core\NativeAdSlotHandlerAbstract;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("NativeAdSlot")
 */
class NativeAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all native ad slots
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "nativeadslot.summary","slotlib.summary", "librarynativeadslot.summary" , "site.summary" , "user.summary"}
     * )
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return NativeAdSlotInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single native AdSlot for the given id
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "nativeadslot.detail","slotlib.summary",  "librarynativeadslot.detail" , "site.summary" , "user.summary"}
     * )
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
     * @return NativeAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @param int $id
     * @return View
     */
    public function getJstagAction($id)
    {
        /** @var NativeAdSlotInterface $nativeAdSlot */
        $nativeAdSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createJsTags($nativeAdSlot);
    }


    /**
     * Create a native AdSlot from the submitted data
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
     * clone the current native ad slot
     *
     * @Rest\POST("/nativeadslots/{id}/clone", requirements={"id" = "\d+"})
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postCloneAction(Request $request, $id)
    {
        /** @var NativeAdSlotInterface $originAdSlot */
        $originAdSlot = $this->one($id);
        $newName = $request->request->get('name');

        if(null === $newName || empty($newName) || !is_string($newName)){
            return $this->view(null, Codes::HTTP_BAD_REQUEST);
        }

        $siteId = $request->request->get('site');
        $site = null != $siteId ? $this->get('tagcade.domain_manager.site')->find($siteId) : null;

        if($site instanceof SiteInterface) {
            $this->checkUserPermission($site, 'edit');
            $this->get('tagcade_api.service.tag_library.clone_ad_slot_service')->cloneAdSlot($originAdSlot, $newName, $site);
        }
        else {
            $this->get('tagcade_api.service.tag_library.clone_ad_slot_service')->cloneAdSlot($originAdSlot, $newName);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Update an existing native AdSlot from the submitted data or create a new native AdSlot
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
     * Update an existing native AdSlot from the submitted data or create a new native AdSlot at a specific location
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
        if(array_key_exists('libraryAdSlot', $request->request->all()))
        {
            $libraryAdSlot = (int)$request->request->get('libraryAdSlot');
            /**
             * @var NativeAdSlotInterface $adSlot
             */
            $adSlot = $this->getOr404($id);

            if($adSlot->getLibraryAdSlot()->getId() !== $libraryAdSlot) {
                $newLibraryAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($libraryAdSlot);

                if(!$newLibraryAdSlot instanceof LibraryNativeAdSlotInterface) {
                    throw new InvalidArgumentException('LibraryAdSlot not existed');
                }

                $this->checkUserPermission($newLibraryAdSlot);

                // create new ad tags
                $this->get('tagcade_api.service.tag_library.replicator')->replicateFromLibrarySlotToSingleAdSlot($newLibraryAdSlot, $adSlot);
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing native AdSlot
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
        /**
         * @var NativeAdSlotInterface $entity
         */
        $entity = $this->getOr404($id);
        $this->checkUserPermission($entity, 'edit');

        // dynamic ad slots that its expressions refer to this ad slot

        $expressions = $this->get('tagcade.repository.expression')->findBy(array('expectAdSlot' => $entity));
        $defaultSlots = $this->get('tagcade.repository.dynamic_ad_slot')->findBy(array('defaultAdSlot' => $entity));

        if (count($expressions) > 0 || count($defaultSlots) > 0) { // this ensures that there is existing dynamic slot that one of its expressions containing this slot
            $view = $this->view('Existing dynamic ad slot that is referencing to this ad slot', Codes::HTTP_BAD_REQUEST);
        }
        else {
            $this->getHandler()->delete($entity);
            $view = $this->view(null, Codes::HTTP_NO_CONTENT);
        }

        return $this->handleView($view);
    }

    /**
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "nativeadslot.summary", "site.summary", "user.summary", "libraryadtag.summary", "adnetwork.summary"}
     * )
     * @param $id
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsAction($id)
    {
        /** @var NativeAdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForAdSlot($adSlot);
    }

    protected function getResourceName()
    {
        return 'nativeadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_nativeadslot';
    }

    /**
     * @return NativeAdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.native_ad_slot');
    }

    /**
     * compare AdTag By Position
     *
     * @param AdTagInterface $adTag_1
     * @param AdTagInterface $adTag_2
     * @return int -1 if  0 1
     */
    protected function compareAdTagByPosition(AdTagInterface $adTag_1, AdTagInterface $adTag_2)
    {
        return $adTag_1->getPosition() < $adTag_2->getPosition() ? -1 : $adTag_1->getPosition() > $adTag_2->getPosition() ? 1 : 0;
    }
}