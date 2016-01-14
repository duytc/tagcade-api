<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("Adslot")
 */
class AdSlotController extends FOSRestController implements ClassResourceInterface
{

    /**
     *
     * Get all ad slots
     * @Rest\View(
     *      serializerGroups={"libraryexpression.detail", "expression.detail", "adslot.detail", "nativeadslot.summary", "displayadslot.summary", "dynamicadslot.summary", "site.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "slotlib.summary"}
     * )
     * @ApiDoc(
     *  section = "Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return BaseAdSlotInterface[]
     */
    public function cgetAction()
    {
        $role = $this->getUser();
        $adSlotManager = $this->get('tagcade.domain_manager.ad_slot');

        if ($role instanceof PublisherInterface) {
            $adSlots = $adSlotManager->getAdSlotsForPublisher($role);
            return $adSlots;
        }

        $adSlots =  $adSlotManager->all();

        return $adSlots;
    }

    /**
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

}