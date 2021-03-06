<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\Handlers\Core\LibrarySlotTagHandlerAbstract;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;

/**
 * @Rest\RouteResource("LibrarySlotTag")
 */
class LibrarySlotTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all library ad slots ad tag
     * @Rest\View(serializerGroups={"libraryslottag.summary", "libraryadtag.summary", "adnetwork.summary", "slotlib.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "librarynativeadslot.summary", "user.summary"})
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section="Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $request
     * @return LibrarySlotTagInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        /** @var LibrarySlotTagRepositoryInterface $librarySlotTagRepository */
        $librarySlotTagRepository = $this->get('tagcade.repository.library_slot_tag');
        if ($request->query->count() < 1) {
            if ($role instanceof PublisherInterface) {
                return $librarySlotTagRepository->getLibrarySlotTagForPublisher($role);
            }

            return $this->all();
        }

        $qb = $librarySlotTagRepository->getLibrarySlotTagForUserWithPagination($this->getUser(), $this->getParams());
        return $this->getPagination($qb, $request);
    }

    /**
     * Get a single library ad slots ad tag for the given id
     * @Rest\View(serializerGroups={"libraryslottag.detail", "libraryadtag.summary", "adnetwork.summary", "slotlib.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "librarynativeadslot.summary", "user.summary"})
     * @ApiDoc(
     *  section="Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibrarySlotTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }


    /**
     * Create a library ad slots ad tag from the submitted data
     *
     * @ApiDoc(
     *  section="Library Ad Slots",
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
     * Update an existing library ad slots ad tag from the submitted data or create a new library ad slots ad tag
     *
     * @ApiDoc(
     *  section="Library Ad Slots",
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
     * Update an existing library ad slots ad tag from the submitted data or create a new library ad slots ad tag at a specific location
     *
     * @ApiDoc(
     *  section="Library Ad Slots",
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
     * Delete an existing library ad slots ad tag
     *
     * @ApiDoc(
     *  section="Library Ad Slots",
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
        return 'libraryslottag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_libraryslottag';
    }

    /**
     * @return LibrarySlotTagHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_slot_tag');
    }
}