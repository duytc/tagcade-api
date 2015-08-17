<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\PersistentCollection;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlot;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\TagLibrary\AdSlotGenerator;

/**
 * @Rest\RouteResource("LibraryAdslot")
 */
class LibraryAdSlotController extends FOSRestController implements ClassResourceInterface
{

    /**
     *
     * Get all library ad slots
     *
     * @Rest\View(
     *      serializerGroups={"slotlib.extra", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return BaseLibraryAdSlotInterface[]
     */
    public function cgetAction()
    {
        $role = $this->getUser();
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            $libraryAdSlots =  $libraryAdSlotManager->getAdSlotsForPublisher($role);

            return $libraryAdSlots;
        }

        return $libraryAdSlotManager->all();
    }

    /**
     * @Rest\View(
     *      serializerGroups={"slotlib.summary", "librarynativeadslot.detail", "librarydisplayadslot.detail", "librarydynamicadslot.detail", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.detail"}
     * )
     * Get a single library adSlot for the given id
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
        $libraryAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($id);


        if (null === $libraryAdSlot) {
            throw new NotFoundHttpException(sprintf('not found ad slot with id %s', $id));
        }
        
        $securityContext = $this->get('security.context');
        if (false === $securityContext->isGranted('view', $libraryAdSlot)) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to view this ad slot or it does not exist',
                    'view',
                    'ad'
                )
            );
        }

        return $libraryAdSlot;
    }

    /**
     * @Rest\Get("/libraryadslots/unreferred/site/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
     * )
     *
     */
    public function getUnreferredAction($id){
        $site = $this->get('tagcade.domain_manager.site')->find($id);
        if(!$site instanceof SiteInterface) {
            throw new \Tagcade\Exception\InvalidArgumentException('Site not existed');
        }

        return $this->get('tagcade.domain_manager.library_ad_slot')->getUnReferencedLibraryAdSlotForSite($site);
    }
}