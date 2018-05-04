<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\DomainManager\LibraryNativeAdSlotManagerInterface;
use Tagcade\Exception\PublicSimpleException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryNativeAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

/**
 * @Rest\RouteResource("LibraryNativeAdSlot")
 */
class LibraryNativeAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.summary", "slotlib.extra", "user.min", "nativeadslot.summary", "site.summary"}
     * )
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * Get all native ad slots
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return LibraryNativeAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /**
         * @var LibraryNativeAdSlotRepositoryInterface $libraryAdSlotRepository
         */
        $libraryAdSlotRepository = $this->get('tagcade.repository.library_native_ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $libraryAdSlotRepository->getLibraryAdSlotsWithPagination($this->getUser(), $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $this->getAllLibraryAdSlot($role);
    }

    protected function getAllLibraryAdSlot($role)
    {
        /**
         * @var LibraryNativeAdSlotManagerInterface $libraryAdSlotManager
         */
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_native_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getLibraryNativeAdSlotsForPublisher($role);
        }

        return $libraryAdSlotManager->all();
    }

    /**
     *
     * Get a single native ad slots for the given id
     *
     * @Rest\View(
     *      serializerGroups={"librarynativeadslot.detail", "slotlib.summary", "user.summary", "nativeadslot.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
     * @Rest\View(serializerGroups={"libraryadtag.summary", "adnetwork.summary", "user.summary", "adtag.summary"})
     * Get a single library adSlot for the given id
     *
     * @ApiDoc(
     *   section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibraryAdTagInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getLibraryadtagAction($id)
    {
        $libraryAdSlot = $this->one($id);

        return $this->get('tagcade.repository.library_ad_tag')->getLibraryAdTagsForLibraryAdSlot($libraryAdSlot);
    }

    /**
     * add an new adtag library to the current native ad slots
     *
     * @Rest\POST("/librarynativeadslots/{id}/adtag", requirements={"id" = "\d+"})
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagAction(Request $request, $id)
    {
        $request->request->set('libraryAdSlot', $id);

        $request->request->set('refId', uniqid("", true));
        // move the creating WaterfallTag to library
        $libraryAdTag = $request->request->get('libraryAdTag');

        if (is_array($libraryAdTag)) {
            $libraryAdTag['visible'] = true;
            $request->request->set('libraryAdTag', $libraryAdTag);
        }

        $this->get('tagcade_api.handler.library_slot_tag')->post($request->request->all());

        return $this->view(null, Codes::HTTP_CREATED);
    }


    /**
     * Create a native ad slots from the submitted data
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
        if (!array_key_exists('visible', $request->request->all())) {
            $request->request->add(array('visible' => true));
        }

        //get Channels from request and override to request
        $request->request->set('channels', $this->getChannels($request->request->get('channels', [])));

        //get Sites from request and override to request
        $request->request->set('sites', $this->getSites($request->request->get('sites', [])));

        return $this->post($request);
    }

    /**
     * Update an existing native ad slots from the submitted data or create a new one at a specific location
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
     * Get those AdSlots which refer to the current AdSlot Library
     * @Rest\View(
     *      serializerGroups={"adslot.summary" , "slotlib.summary", "user.summary", "nativeadslot.summary", "librarynativeadslot.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
    public function getAssociatedadslotsAction($id)
    {
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getAdSlots();
    }

    /**
     * Get those AdTags which belong to the given AdSlot Library, also have been moved to WaterfallTag Library
     *
     * @Rest\View(
     *      serializerGroups={"libraryslottag.summary", "libraryadtag.summary", "librarynativeadslot.summary", "slotlib.extra", "user.min", "nativeadslot.summary", "site.summary"}
     * )
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param Request $request
     * @param int $id the resource id
     *
     * @return AdTagInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdtagsAction(Request $request, $id)
    {
        /** @var LibraryNativeAdSlotInterface $entity */
        $entity = $this->one($id);
        /**
         * @var LibrarySlotTagRepositoryInterface $librarySlotTagRepository
         */
        $librarySlotTagRepository = $this->get('tagcade.repository.library_slot_tag');

        if ($request->query->get('page') > 0) {
            $qb = $librarySlotTagRepository->getByLibraryAdSlotWithPagination($entity, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $librarySlotTagRepository->getByLibraryAdSlot($entity);
    }

    /**
     * Delete an existing adTag library
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
        /** @var LibraryNativeAdSlotInterface $libraryNativeAdSlot */
        $libraryNativeAdSlot = $this->getOr404($id);

        $referencingSlots = $libraryNativeAdSlot->getAdSlots()->toArray();
        if (count($referencingSlots) > 0) {
            throw new PublicSimpleException('There are some slots still referring to this library');
        }

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