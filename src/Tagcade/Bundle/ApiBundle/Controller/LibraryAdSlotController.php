<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Handler\HandlerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * @Rest\RouteResource("LibraryAdslot")
 */
class LibraryAdSlotController extends RestControllerAbstract implements ClassResourceInterface
{

//    /**
//     *
//     * Get all library ad slots
//     * @Rest\View(
//     *      serializerGroups={"slotlib.extra", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
//     * )
//     * @ApiDoc(
//     *  resource = true,
//     *  statusCodes = {
//     *      200 = "Returned when successful"
//     *  }
//     * )
//     *
//     * @return BaseLibraryAdSlotInterface[]
//     */
//    public function cgetAction()
//    {
//        $role = $this->getUser();
//        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');
//
//        if ($role instanceof PublisherInterface) {
//            $libraryAdSlots =  $libraryAdSlotManager->getAdSlotsForPublisher($role);
//
//            return $libraryAdSlots;
//        }
//
//        return $libraryAdSlotManager->all();
//    }

    /**
     * @Rest\Get("/libraryadslots")
     * @Rest\QueryParam(name="forRon", requirements="(true|false)", nullable=true)
     *
     * @Rest\View(
     *      serializerGroups={"slotlib.extra", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.detail", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.detail"}
     * )
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *      200 = "Returned when successful"
     *   }
     * )
     *
     */
    public function cgetAction()
    {
        $role = $this->getUser();
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $forRon = $paramFetcher->get('forRon');

        if ($forRon === null) {
            return $this->getAllLibraryAdSlot($role);
        }
        else {
            $forRon = filter_var($forRon, FILTER_VALIDATE_BOOLEAN);
            if ($forRon) {
                return $this->getAllLibraryAdSlotForRonAdSlot($role);
            }
            else {
                return $this->getAllLibraryAdSlotNotForRonAdSlot($role);
            }
        }
    }

    protected function getAllLibraryAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return  $libraryAdSlotManager->getAdSlotsForPublisher($role);
        }

        return $libraryAdSlotManager->all();
    }

    public function getAllLibraryAdSlotForRonAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return  $libraryAdSlotManager->getLibraryAdSlotsUnusedInRonForPublisher($role);
        }

        return $libraryAdSlotManager->getAllLibraryAdSlotsUnusedInRon();
    }

    public function getAllLibraryAdSlotNotForRonAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return  $libraryAdSlotManager->getLibraryAdSlotsUsedInRonForPublisher($role);
        }

        return $libraryAdSlotManager->getAllLibraryAdSlotsUsedInRon();
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

    /**
     * @Rest\Post("/libraryadslots/{id}/createlinks", requirements={"id" = "\d+"})
     *
     * @param Request $request
     *
     * @param int $id
     *
     * @return view
     */
    public function postCreateLinksAction(Request $request, $id)
    {
        //find and validate slotLibrary
        /** @var BaseLibraryAdSlotInterface $slotLibrary */
        $slotLibrary = $this->get('tagcade.domain_manager.library_ad_slot')->find($id);
        if(!$slotLibrary instanceof BaseLibraryAdSlotInterface) {
            throw new NotFoundHttpException(
                sprintf("The %s resource '%s' was not found or you do not have access", $this->getResourceName(), $id)
            );
        }

        $this->checkUserPermission($slotLibrary, 'view');
        //get params as channelIds and siteIds
        $allParams = $request->request->all();
        $channelIds = $allParams['channels'];
        $siteIds = $allParams['sites'];

        //get Channels And Validate Permission
        /** @var ChannelInterface[] $channels */
        $channels = $this->getAndValidatePermissionForChannels($channelIds);
        //get Sites And Validate Permission
        /** @var SiteInterface[] $sites */
        $sites = $this->getAndValidatePermissionForSites($siteIds);

        $this->get('tagcade_api.service.tag_library.ad_slot_generator_service')->generateAdSlotFromLibraryForChannelsAndSites($slotLibrary, $channels, $sites);

        return $this->view(null, Codes::HTTP_CREATED);
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'libraryadslot';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_libraryadslot';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        throw new RuntimeException('Not support get handler of for common libraryAdSlot.
        Go to either LibraryDisplayAdSlotController, LibraryNativeAdSlotController or LibraryDynamicAdSlotController and use associate handlers.');
    }

    /**
     * get And Validate Permission For Channels
     * @param array $channelIds
     * @return ChannelInterface[]
     * @throws NotFoundHttpException if $user have no Permission on any Channels
     */
    private function getAndValidatePermissionForChannels(array $channelIds)
    {
        $channels = [];
        $channelManager = $this->get('tagcade.domain_manager.channel');

        array_walk(
            $channelIds, function($channelId) use($channelManager, &$channels){
                $channel = $channelManager->find((int)$channelId);

                if (!$channel instanceof ChannelInterface) {
                    throw new NotFoundHttpException('Some channels are not found');
                }

                if (!in_array($channel, $channels)) {
                    $this->checkUserPermission($channel, 'edit');
                    $channels[] = $channel;
                }
            }
        );

        return $channels;
    }

    /**
     * get And Validate Permission For Sites
     * @param array $siteIds
     * @return SiteInterface[]
     * @throws NotFoundHttpException if $user have no Permission on any Sites
     */
    private function getAndValidatePermissionForSites(array $siteIds)
    {
        /** @var SiteInterface[] $sites */
        $sites = [];
        /** @var SiteManagerInterface $siteManager */
        $siteManager = $this->get('tagcade.domain_manager.site');
        array_walk(
            $siteIds,
            function($siteId) use($siteManager, &$sites) {
                $site = $siteManager->find((int)$siteId);

                if (!$site instanceof SiteInterface) {
                    throw new NotFoundHttpException('Some channels are not found');
                }

                if (!in_array($site, $sites)) {
                    $this->checkUserPermission($site, 'edit');
                    $sites[] = $site;
                }
            }
        );

        return $sites;
    }

    /**
     * filter Sites Existed In Channels
     * @param ChannelInterface[] $channels
     * @param SiteInterface[] $sites
     * @return array
     */
    private function filterSitesExistedInChannels(array $channels, array $sites)
    {
        return array_filter(
            $sites,
            function (SiteInterface $site) use ($channels) {
                $channelsOfSite = $site->getChannels();
                foreach ($channelsOfSite as $cn) {
                    if (in_array($cn, $channels)) {
                        return false; // ignore this site since it is in input channel already
                    }
                }

                return true;
            }
        );
    }
}