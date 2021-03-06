<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\DomainManager\LibraryDisplayAdSlotManagerInterface;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibraryDisplayAdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

/**
 * @Rest\RouteResource("LibraryDisplayAdSlot")
 */
class LibraryDisplayAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all library displays adslots
     *
     * @Rest\View(
     *      serializerGroups={"librarydisplayadslot.summary" , "slotlib.extra", "user.min", "displayadslot.summary", "site.summary"}
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
     * @return LibraryDisplayAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /**
         * @var LibraryDisplayAdSlotRepositoryInterface $libraryAdSlotRepository
         */
        $libraryAdSlotRepository = $this->get('tagcade.repository.library_display_ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $libraryAdSlotRepository->getLibraryAdSlotsWithPagination($this->getUser(), $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $this->getAllLibraryAdSlot($role);
    }

    protected function getAllLibraryAdSlot($role)
    {
        /**
         * @var LibraryDisplayAdSlotManagerInterface $libraryAdSlotManager
         */
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_display_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getLibraryDisplayAdSlotsForPublisher($role);
        }

        return $libraryAdSlotManager->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"librarydisplayadslot.detail" , "slotlib.summary", "user.summary", "displayadslot.summary", "site.summary"}
     * )
     *
     * Get a single library displays adslot for the given id
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
     * Update an existing library displays adslot from the submitted data or create a new one at a specific location
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
     * Update the position of all ad tags in an library displays adslot
     * @Rest\View(
     *      serializerGroups={"libraryslottag.summary", "libraryadtag.summary", "librarydisplayadslot.summary", "slotlib.extra", "user.summary", "displayadslot.summary", "site.summary"}
     * )
     * @ApiDoc(
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function postAdtagsPositionsAction(Request $request, $id)
    {
        /** @var LibraryDisplayAdSlotInterface $libraryAdSlot */
        $libraryAdSlot = $this->one($id);
        $newAdTagOrderIds = $request->request->get('ids');

        if (!$newAdTagOrderIds) {
            throw new BadRequestHttpException("Ad tagIds parameter is required");
        }


        $result = array_values(
            $this->get('tagcade_app.service.core.ad_tag.ad_tag_position_editor')
                ->setAdTagPositionForLibraryAdSlot($libraryAdSlot, $newAdTagOrderIds)
        );

        return $result;
    }

    /**
     * Update the position of all ad tags in an library displays adslot
     *
     * @Rest\POST("/librarydisplayadslots/{id}/adtag", requirements={"id" = "\d+"})
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
        if(is_array($libraryAdTag)){
            $libraryAdTag['visible'] = true;
            $request->request->set('libraryAdTag', $libraryAdTag);
        }

        $this->get('tagcade_api.handler.library_slot_tag')->post($request->request->all());

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * Create a adSlot library from the submitted data
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
     * Get those AdSlots which refer to the current AdSlot Library
     * @Rest\View(
     *      serializerGroups={"adslot.summary" , "slotlib.summary", "user.summary", "displayadslot.summary", "librarydisplayadslot.summary", "site.summary"}
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
     * @return DisplayAdSlotInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAssociatedadslotsAction($id){
        /** @var LibraryDisplayAdSlotInterface $entity */
        $entity = $this->one($id);

        return $entity->getAdSlots();
    }

    /**
     * Get those AdTags which belong to the given display AdSlot Library
     * @Rest\View(
     *      serializerGroups={"libraryslottag.detail" , "slotlib.detail", "librarydisplayadslot.detail", "libraryadtag.detail", "user.min"}
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
        /** @var LibraryDisplayAdSlotInterface $entity */
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
        /** @var LibraryDisplayAdSlotInterface $libraryDisplayAdSlot */
        $libraryDisplayAdSlot = $this->getOr404($id);

        $referencingSlots = $libraryDisplayAdSlot->getAdSlots()->toArray();
        if (count($referencingSlots) > 0) {
            throw new BadRequestHttpException('There are some slots still referring to this library');
        }

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