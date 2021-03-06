<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\DomainManager\LibraryDynamicAdSlotManagerInterface;
use Tagcade\Handler\Handlers\Core\LibraryDynamicAdSlotHandlerAbstract;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDynamicAdSlotRepositoryInterface;

/**
 * @Rest\RouteResource("LibraryDynamicAdSlot")
 */
class LibraryDynamicAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all library dynamic adSlot
     *
     * @Rest\View(
     *      serializerGroups={"librarydynamicadslot.summary" , "slotlib.extra", "user.min", "dynamicadslot.summary", "site.summary", "expression.detail", "adslot.summary", "displayadslot.summary", "nativeadslot.summary" , "libraryexpression.detail"}
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
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @param Request $request
     * @return LibraryDynamicAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /**
         * @var LibraryDynamicAdSlotRepositoryInterface $libraryAdSlotRepository
         */
        $libraryAdSlotRepository = $this->get('tagcade.repository.library_dynamic_ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $libraryAdSlotRepository->getLibraryAdSlotsWithPagination($this->getUser(), $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $this->getAllLibraryAdSlot($role);
    }

    protected function getAllLibraryAdSlot($role)
    {
        /**
         * @var LibraryDynamicAdSlotManagerInterface $libraryAdSlotManager
         */
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_dynamic_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getLibraryDynamicAdSlotsForPublisher($role);
        }

        return $libraryAdSlotManager->all();
    }

    /**
     * Get a single library dynamic adSlot for the given id
     * @Rest\View(
     *      serializerGroups={"librarydynamicadslot.detail" , "slotlib.summary", "user.summary", "dynamicadslot.summary", "site.summary", "expression.detail", "displayadslot.summary", "nativeadslot.summary", "adslot.summary", "libraryexpression.detail"}
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
     * @return LibraryDynamicAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a library dynamic adSlot from the submitted data
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
        if(!array_key_exists('visible', $request->request->all()))
        {
            $request->request->add(array('visible' => true));
        }

        //get Channels from request and override to request
        $request->request->set('channels', $this->getChannels($request->request->get('channels', [])));

        //get Sites from request and override to request
        $request->request->set('sites', $this->getSites($request->request->get('sites', [])));

        return $this->post($request);
    }

    /**
     * Update an existing library dynamic adSlot from the submitted data or create a new one at a specific location
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
        $params = $request->request->all();

        if (array_key_exists('visible', $params) && false == $params['visible']) {
            /**
             * @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot;
             */
            $libraryDynamicAdSlot = $this->getOr404($id);
            $referencingSlots = $libraryDynamicAdSlot->getAdSlots()->toArray();
            if (count($referencingSlots) > 0) {
                throw new BadRequestHttpException('There are some slots still referring to this library');
            }

        }

        return $this->patch($request, $id);
    }


    /**
     * Get those AdSlots which refer to the current library dynamic adSlot
     * @Rest\View(
     *      serializerGroups={"adslot.summary" , "slotlib.summary", "user.summary", "dynamicadslot.summary", "librarydynamicadslot.summary", "site.summary"}
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
     * @return DynamicAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryDynamicAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getAdSlots();
    }


    /**
     * Update an existing library dynamic adSlot from the submitted data or create a new one
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
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
     * Delete an existing adSlot
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
        /** @var LibraryDynamicAdSlotInterface $libraryDynamicAdSlot */
        $libraryDynamicAdSlot = $this->getOr404($id);

        $referencingSlots = $libraryDynamicAdSlot->getAdSlots()->toArray();
        if (count($referencingSlots) > 0) {
            throw new BadRequestHttpException('There are some slots still referring to this library');
        }

        return $this->delete($id);
    }

    protected function getResourceName()
    {
        return 'librarydynamicadslot';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_librarydynamicadslot';
    }

    /**
     * @return LibraryDynamicAdSlotHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->get('tagcade_api.handler.library_dynamic_ad_slot');
    }

}