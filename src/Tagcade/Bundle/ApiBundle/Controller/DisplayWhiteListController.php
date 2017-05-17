<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\DomainManager\DisplayWhiteListManagerInterface;
use Tagcade\Handler\Handlers\Core\DisplayBlacklistHandlerAbstract;
use Tagcade\Handler\Handlers\Core\DisplayWhiteListHandlerAbstract;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\DisplayWhiteListRepositoryInterface;

/**
 * @Rest\RouteResource("DisplayWhiteList")
 */
class DisplayWhiteListController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all display white list
     *
     * @Rest\View(
     *      serializerGroups={"display.whitelist.min", "user.min", "adnetwork.min"}
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
     *  section="DisplayWhiteLists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @param Request $request
     * @return \Tagcade\Model\Core\DisplayWhiteListInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();

        /** @var DisplayWhiteListRepositoryInterface $displayWhiteListRepository */
        $displayWhiteListRepository = $this->get('tagcade.repository.display.white_list');

        /** @var DisplayWhiteListManagerInterface $displayWhiteListManager */
        $displayWhiteListManager = $this->get('tagcade.domain_manager.display.white_list');

        if ($request->query->get('page') > 0) {
            $qb = $displayWhiteListRepository->getDisplayWhiteListsForPublisherWithPagination($role, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $displayWhiteListRepository->getDisplayWhiteListsForPublisher($role)
            : $displayWhiteListManager->all();
    }

    /**
     * Get a single display white list for the given id
     *
     * @Rest\View(
     *      serializerGroups={"display.whitelist.summary", "user.min", "adnetwork.min", "network.whitelist.min"}
     * )
     * @ApiDoc(
     *  section="DisplayWhiteLists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return DisplayWhiteListInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get adNetworks of display white list for the given id
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
        /** @var DisplayWhiteListInterface $displayWhiteList */
        $displayWhiteList = $this->one($id);

        return $displayWhiteList->getAdNetworks();
    }

    /**
     * Create a display white list from the submitted data
     *
     * @ApiDoc(
     *  section="DisplayWhiteLists",
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
     * Update an existing display white list from the submitted data or create a new display white list at a specific location
     *
     * @ApiDoc(
     *  resource = true,
     *  section="DisplayWhiteLists",
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
     * Delete an existing display white list
     *
     * @ApiDoc(
     *  section="DisplayWhiteLists",
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
        return 'display_white_list';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_whitelist';
    }

    /**
     * @return DisplayWhiteListHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.display.white_list');
    }
}
