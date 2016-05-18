<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("Adslot")
 */
class AdSlotController extends RestControllerAbstract implements ClassResourceInterface
{

    /**
     *
     * Get all ad slots
     * @Rest\View(
     *      serializerGroups={"libraryexpression.detail", "expression.detail", "adslot.detail", "nativeadslot.summary", "displayadslot.summary", "dynamicadslot.summary", "site.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "slotlib.summary"}
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
     * @return BaseAdSlotInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $adSlotManager = $this->get('tagcade.domain_manager.ad_slot');
        $adSlotRepository = $this->get('tagcade.repository.ad_slot');

        if ($request->query->get('page') > 0) {
            $qb = $adSlotRepository->getAdSlotsForUserWithPagination($role, $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $adSlotManager->getAdSlotsForPublisher($role)
            : $adSlotManager->all();
    }

    /**
     * @Rest\Get("/adslots/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"libraryexpression.detail", "expression.summary", "adslot.detail", "nativeadslot.detail", "displayadslot.detail", "dynamicadslot.detail", "site.summary", "librarynativeadslot.detail", "librarydisplayadslot.detail", "librarydynamicadslot.summary", "user.summary", "slotlib.summary"}
     * )
     * Get a single adSlot for the given id
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
     * @return BaseAdSlotInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($id);

        if (null === $adSlot) {
            throw new NotFoundHttpException(sprintf('not found ad slot with id %s', $id));
        }
        
        $securityContext = $this->get('security.context');
        if (false === $securityContext->isGranted('view', $adSlot)) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to view this ad slot or it does not exist',
                    'view',
                    'ad'
                )
            );
        }

        return $adSlot;
    }

    /**
     * get AdSlots belong to at least one Channel
     *
     * @Rest\View(
     *      serializerGroups={"libraryexpression.detail", "expression.summary", "adslot.detail", "nativeadslot.withChannel", "displayadslot.withChannel", "dynamicadslot.withChannel", "site.minimum", "channel.summary", "librarynativeadslot.detail", "librarydisplayadslot.detail", "librarydynamicadslot.summary", "user.summary", "slotlib.summary"}
     * )
     *
     * @Rest\Get("/adslots/relatedchannel")
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param Request $request
     * @return array
     */
    public function getAdSlotsRelatedChannelAction( Request $request )
    {
        $adSlotRepository = $this->get('tagcade.repository.ad_slot');
        if($request->query->get('page') >0){
            $qb = $adSlotRepository->getRelatedChannelWithPagination($this->getUser(),$this->getParams());
            return $this->getPagination($qb,$request);
        }

        return $this->get('tagcade.domain_manager.ad_slot')->getAdSlotsRelatedChannelForUser($this->getUser());

    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        // TODO: Implement getResourceName() method.
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        // TODO: Implement getGETRouteName() method.
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        // TODO: Implement getHandler() method.
    }
}