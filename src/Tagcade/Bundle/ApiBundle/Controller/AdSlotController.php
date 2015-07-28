<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("Adslot")
 */
class AdSlotController extends FOSRestController implements ClassResourceInterface
{

    /**
     *
     * Get all ad tags
     *
     * @ApiDoc(
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
     *      serializerGroups={"adslot.detail", "nativeadslot.detail", "site.summary", "nativeadslotlib.ref", "displayadslotlib.ref", "publisher.summary"}
     * )
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     * Get a single adSlot for the given id
     *
     * @ApiDoc(
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