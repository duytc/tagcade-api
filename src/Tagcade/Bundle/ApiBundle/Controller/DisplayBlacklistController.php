<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Handler\Handlers\Core\DisplayBlacklistHandlerAbstract;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\User\Role\PublisherInterface;

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
     * @ApiDoc(
     *  section="DisplayBlacklists",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Tagcade\Model\Core\DisplayBlacklistInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
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
        if ($displayBlacklist->isDefault()){
            return $this->get('tagcade.domain_manager.ad_network')->getAdNetworksForPublisher($displayBlacklist->getPublisher());
        }
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
