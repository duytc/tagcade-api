<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\Handler\Handlers\Core\DisplayBlacklistHandlerAbstract;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\DisplayBlacklistRepositoryInterface;

/**
 * @Rest\RouteResource("DisplayBlacklist")
 */
class DisplayBlacklistController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all display blacklist
     *
     * @Rest\View(
     *      serializerGroups={"display.blacklist.min", "user.min", "adnetwork.min"}
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
     *  section="DisplayBlacklists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Tagcade\Model\Core\DisplayBlacklistInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();

        /** @var DisplayBlacklistRepositoryInterface $displayBlacklistRepository */
        $displayBlacklistRepository = $this->get('tagcade.repository.display.blacklist');

        /** @var DisplayBlacklistManagerInterface $displayBlacklistManager */
        $displayBlacklistManager = $this->get('tagcade.domain_manager.display.blacklist');

        if ($request->query->get('page') > 0) {
            $qb = $displayBlacklistRepository->getDisplayBlacklistsForPublisherWithPagination($role, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $displayBlacklistRepository->getDisplayBlacklistsForPublisher($role)
            : $displayBlacklistManager->all();
    }

    /**
     * Get default display blacklist for publisher
     *
     * @Rest\Get("/displayblacklists/{publisherId}/default")
     *
     * @Rest\View(
     *      serializerGroups={"display.blacklist.summary", "user.min", "adnetwork.min", "network.blacklist.min"}
     * )
     * @ApiDoc(
     *  section="DisplayBlacklists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     * @return \Tagcade\Model\Core\DisplayBlacklistInterface[]
     */
    public function cgetDefaultBlacklistAction($publisherId)
    {
        if ($publisherId){
            $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
            $publisher = $publisherManager->find($publisherId);
        } else{
            $publisher = $this->getUser();
        }

        if (!$publisher instanceof PublisherInterface){
            return [];
        }

        $displayBlacklistManager = $this->get('tagcade.domain_manager.display.blacklist');
        return $displayBlacklistManager->getDefaultBlacklists($publisher);
    }

    /**
     * Get a single display blacklist for the given id
     *
     * @Rest\View(
     *      serializerGroups={"display.blacklist.summary", "user.min", "adnetwork.min", "network.blacklist.min"}
     * )
     * @ApiDoc(
     *  section="DisplayBlacklists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return DisplayBlacklistInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get adNetworks of displayblacklist for the given id
     *
     * @Rest\View(
     *      serializerGroups={"adnetwork.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  section="DisplayBlacklists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\AdNetworkInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdnetworksAction($id)
    {
        /** @var DisplayBlacklistInterface $displayBlacklist */
        $displayBlacklist = $this->one($id);
        return $displayBlacklist->getAdNetworks();
    }

    /**
     * Create a display blacklist from the submitted data
     *
     * @ApiDoc(
     *  section="DisplayBlacklists",
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
     * Update an existing display blacklist from the submitted data or create a new display blacklist at a specific location
     *
     * @ApiDoc(
     *  resource = true,
     *  section="DisplayBlacklists",
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
     * Delete an existing display blacklist
     *
     * @ApiDoc(
     *  section="DisplayBlacklists",
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
        return 'display_blacklist';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_blacklist';
    }

    /**
     * @return DisplayBlacklistHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.display.blacklist');
    }
}
