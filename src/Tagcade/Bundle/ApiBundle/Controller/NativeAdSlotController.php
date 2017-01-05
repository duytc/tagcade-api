<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\UpdateSiteForAdSlotValidator;
use Tagcade\Handler\Handlers\Core\NativeAdSlotHandlerAbstract;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("NativeAdSlot")
 */
class NativeAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use UpdateSiteForAdSlotValidator;

    /**
     * Get all native ad slots
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "nativeadslot.summary", "slotlib.summary", "librarynativeadslot.summary" , "site.summary" , "user.min"}
     * )
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Tagcade\Model\Core\NativeAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $nativeAdSlotRepository = $this->get('tagcade.repository.native_ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $nativeAdSlotRepository->getAdSlotsForUserWithPagination($role, $this->getParams());
            return $this->getPagination($qb, $request);
        }

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
     *  section = "Ad Slots",
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
     * Get js tags for this ad slot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getJstagAction(Request $request, $id)
    {
        $params = $request->query->all();
        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }
        /** @var NativeAdSlotInterface $nativeAdSlot */
        $nativeAdSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createJsTags($nativeAdSlot, $forceSecure);
    }


    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * Create a native AdSlot from the submitted data
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
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
            $this->get('tagcade_api.service.tag_library.ad_slot_cloner_service')->cloneAdSlot($originAdSlot, $newName, $site);
        }
        else {
            $this->get('tagcade_api.service.tag_library.ad_slot_cloner_service')->cloneAdSlot($originAdSlot, $newName);
        }

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Update an existing native AdSlot from the submitted data or create a new native AdSlot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     *  section = "Ad Slots",
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
        /** @var BaseAdSlotInterface $adSlot */
        $adSlot = $this->one($id);
        $this->validateSiteWhenUpdatingAdSlot($request, $adSlot);

        if(array_key_exists('libraryAdSlot', $request->request->all()))
        {
            if(!is_array($request->request->get('libraryAdSlot'))) {
                $libraryAdSlotId = (int)$request->request->get('libraryAdSlot');
                /**
                 * @var NativeAdSlotInterface $adSlot
                 */
                $adSlot = $this->getOr404($id);

                $newLibraryAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($libraryAdSlotId);

                if(!$newLibraryAdSlot instanceof LibraryNativeAdSlotInterface) {
                    throw new InvalidArgumentException('LibraryAdSlot not existed');
                }

                $this->checkUserPermission($newLibraryAdSlot);

                if($adSlot->getLibraryAdSlot()->getId() !== $libraryAdSlotId && $newLibraryAdSlot->isVisible()) {
                    // create new ad tags
                    $this->get('tagcade_api.service.tag_library.replicator')->replicateFromLibrarySlotToSingleAdSlot($newLibraryAdSlot, $adSlot);
                }
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing native AdSlot
     *
     * @ApiDoc(
     *  section = "Ad Slots",
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
     * Get ad tags for this ad slot
     *
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "nativeadslot.summary", "site.summary", "user.summary", "libraryadtag.summary", "adnetwork.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsAction($id)
    {
        /** @var NativeAdSlotInterface|ReportableAdSlotInterface $adSlot */
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
     * compare WaterfallTag By Position
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