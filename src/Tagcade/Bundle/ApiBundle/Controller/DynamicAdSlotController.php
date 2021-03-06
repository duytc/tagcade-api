<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\UpdateSiteForAdSlotValidator;
use Tagcade\Handler\Handlers\Core\DynamicAdSlotHandlerAbstract;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("DynamicAdSlot")
 */
class DynamicAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use UpdateSiteForAdSlotValidator;

    /**
     * Get all dynamic ad slots
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "dynamicadslot.summary", "librarydynamicadslot.detail" , "site.summary" , "user.min", "expression.detail", "libraryexpression.detail", "libraryExpression.summary", "displayadslot.summary", "nativeadslot.summary", "librarydisplayadslot.summary", "librarynativeadslot.summary", "slotlib.summary"}
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
     * @return \Tagcade\Model\Core\DynamicAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $dynamicAdSlotRepository = $this->get('tagcade.repository.dynamic_ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $dynamicAdSlotRepository->getAdSlotsForUserWithPagination($role, $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return $this->all();
    }

    /**
     * Get a single dynamic adSlot for the given id
     *
     * @Rest\Get("/dynamicadslots/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "dynamicadslot.detail", "librarydynamicadslot.detail" , "site.summary" , "user.summary", "expression.detail", "libraryexpression.detail", "libraryExpression.summary", "displayadslot.summary", "nativeadslot.summary", "librarydisplayadslot.summary", "librarynativeadslot.summary", "slotlib.summary"}
     * )
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
     * @return DynamicAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @deprecated
     *
     * @Rest\Get("/dynamicadslots/prospective")
     *
     * @Rest\View(
     *      serializerGroups={"adslot.detail", "dynamicadslot.detail", "librarydynamicadslot.detail" , "site.summary" , "user.summary", "expression.detail", "libraryexpression.detail", "libraryExpression.summary", "displayadslot.summary", "nativeadslot.summary", "librarydisplayadslot.summary", "librarynativeadslot.summary", "slotlib.summary"}
     * )
     *
     * @Rest\QueryParam(name="site")
     * @Rest\QueryParam(name="library")
     *
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
     * @return DynamicAdSlotInterface
     *
     */
    public function getProspectiveAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        $siteId = (int)$paramFetcher->get('site');
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site instanceof SiteInterface) {
            throw new NotFoundHttpException(sprintf('not found any site  with id %s', $siteId));
        }

        $this->checkUserPermission($site);

        $libraryId = (int)$paramFetcher->get('library');
        $libraryAdSlot = $this->get('tagcade.domain_manager.library_dynamic_ad_slot')->find($libraryId);

        if (!$libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
            throw new NotFoundHttpException(sprintf('not found any site  with id %s', $siteId));
        }

        $this->checkUserPermission($libraryAdSlot);

        return $this->get('tagcade_api.service.tag_library.ad_slot_generator_service')->getProspectiveDynamicAdSlotForLibraryAndSite($libraryAdSlot, $site);
    }


    /**
     * @Rest\View(serializerEnableMaxDepthChecks=true)
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
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function getJstagAction(Request $request, $id)
    {
        $params = $request->query->all();
        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }

        /** @var DynamicAdSlotInterface $adSlot */
        $adSlot = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->createJsTags($adSlot, $forceSecure);
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * Create a dynamic adSlot from the submitted data
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
     * Update an existing dynamic adSlot from the submitted data or create a new dynamic adSlot
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
     * Update an existing dynamic adSlot from the submitted data or create a new dynamic adSlot at a specific location
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

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing dynamic adSlot
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


    protected function getResourceName()
    {
        return 'dynamicadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_dynamicadslot';
    }

    /**
     * @return DynamicAdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->get('tagcade_api.handler.dynamic_ad_slot');
    }

}