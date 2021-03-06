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
use Tagcade\Exception\InvalidArgumentException;
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
    /**
     * Get all library ad slots
     *
     * @Rest\Get("/libraryadslots")
     * @Rest\QueryParam(name="forRon", requirements="(true|false)", nullable=true)
     *
     * @Rest\View(
     *      serializerGroups={"slotlib.extra", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.detail", "user.min", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.detail"}
     * )
     *
     *      *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="publisherId", nullable=true, description="the publisher id which is used for filtering library ad slots")
     *
     * @ApiDoc(
     *   section = "Library Ad Slots",
     *   resource = true,
     *   statusCodes = {
     *      200 = "Returned when successful"
     *   }
     * )
     * @param Request $request
     * @return array|mixed|\Tagcade\Model\Core\BaseLibraryAdSlotInterface[]|\Tagcade\Model\ModelInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $forRon = $paramFetcher->get('forRon');
        $libraryAdSlotRepository = $this->get('tagcade.repository.library_ad_slot');

        if ($forRon === null) {
            if ($request->query->get('page') > 0) {
                $qb = $libraryAdSlotRepository->getLibraryAdSlotsWithPagination($this->getUser(), $this->getParams());

                return $this->getPagination($qb, $request);
            }

            return $this->getAllLibraryAdSlot($role);
        } else {
            $forRon = filter_var($forRon, FILTER_VALIDATE_BOOLEAN);

            if ($forRon) {
                return $this->getAllLibraryAdSlotForRonAdSlot($role);
            } else {
                return $this->getAllLibraryAdSlotNotForRonAdSlot($role);
            }
        }
    }

    protected function getAllLibraryAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getAdSlotsForPublisher($role);
        }

        return $libraryAdSlotManager->all();
    }

    public function getAllLibraryAdSlotForRonAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getLibraryAdSlotsUnusedInRonForPublisher($role);
        }

        return $libraryAdSlotManager->getAllLibraryAdSlotsUnusedInRon();
    }

    public function getAllLibraryAdSlotNotForRonAdSlot($role)
    {
        $libraryAdSlotManager = $this->get('tagcade.domain_manager.library_ad_slot');

        if ($role instanceof PublisherInterface) {
            return $libraryAdSlotManager->getLibraryAdSlotsUsedInRonForPublisher($role);
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
     * Get library ad slots that are unlinked to a site
     * @deprecated
     *
     * @Rest\Get("/libraryadslots/unreferred/site/{id}", requirements={"id" = "\d+"})
     * @Rest\View(
     *      serializerGroups={"slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
     * )
     *
     * @ApiDoc(
     *   section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     * @param int $id
     * @return array
     */
    public function getUnreferredAction($id)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($id);
        if (!$site instanceof SiteInterface) {
            throw new InvalidArgumentException('Site not existed');
        }

        return $this->get('tagcade.domain_manager.library_ad_slot')->getUnReferencedLibraryAdSlotForSite($site);
    }

    /**
     *
     * Create linked ad slot from library ad slot
     *
     * @Rest\Post("/libraryadslots/{id}/createlinks", requirements={"id" = "\d+"})
     *
     *
     * @ApiDoc(
     *  section = "Library Ad Slots",
     *  resource = true,
     *  statusCodes = {
     *      201 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
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
        if (!$slotLibrary instanceof BaseLibraryAdSlotInterface) {
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
            $channelIds, function ($channelId) use ($channelManager, &$channels) {
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
            function ($siteId) use ($siteManager, &$sites) {
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
     * get Blacklist belong to LibAdSlot
     *
     * @Rest\View(
     *     serializerGroups={"display.blacklist.min", "user.min", "adnetwork.min","slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
     * )
     *
     * @Rest\Get("/libraryadslots/{id}/displayblacklists")
     *
     * @param Request $request
     * @return array
     */
    public function getBlackListForLibAdSlotAction(Request $request, $id)
    {

        /* find ad slot */
        /** @var BaseLibraryAdSlotInterface $libAdSlot */
        $libAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($id);

        if (!$libAdSlot instanceof BaseLibraryAdSlotInterface) {
            throw new NotFoundHttpException('Library Ad Slot is not found');
        }
        /* check permission */
        $this->checkUserPermission($libAdSlot, 'view');

        /* get black lists */
        $displayBlacklistRepository = $this->get('tagcade.repository.display.blacklist');
        $displayBlackLists = $displayBlacklistRepository->getBlacklistForLibAdSlot($libAdSlot);

        return $displayBlackLists;
    }

    /**
     * get Whitelist belong to LibAdSlot
     *
     * @Rest\View(
     *     serializerGroups={"display.whitelist.min", "user.min", "adnetwork.min","slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "librarydynamicadslot.summary", "user.summary", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.summary"}
     * )
     *
     * @Rest\Get("/libraryadslots/{id}/displaywhitelists")
     *
     * @param Request $request
     * @return array
     */
    public function getWhiteListForLibAdSlotAction(Request $request, $id)
    {
        /* find ad slot */
        /** @var BaseLibraryAdSlotInterface $libAdSlot */
        $libAdSlot = $this->get('tagcade.domain_manager.library_ad_slot')->find($id);

        if (!$libAdSlot instanceof BaseLibraryAdSlotInterface) {
            throw new NotFoundHttpException('Library Ad Slot is not found');
        }
        /* check permission */
        $this->checkUserPermission($libAdSlot, 'view');

        /* get white lists */
        $displayWhitelistRepository = $this->get('tagcade.repository.display.white_list');
        $displayWhiteLists = $displayWhitelistRepository->getWhitelistForLibAdSlot($libAdSlot);

        return  $displayWhiteLists;

    }
}