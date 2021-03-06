<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

/**
 * @Rest\RouteResource("Site")
 */
class SiteController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all sites
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.min", "publisherexchange.summary", "exchange.summary"}
     * )
     *
     * @Rest\QueryParam(name="autoCreate", nullable=true)
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="publisherId", nullable=true, description="the publisher id which is used for filtering sites")
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     * @param $request
     * @return SiteInterface[]
     */
    public function cgetAction(Request $request)
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        if ($request->query->count() < 1) {
            return $this->all();
        }

        /** @var SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->get('tagcade.repository.site');
        $autoCreate = null;
        if (is_string($request->query->get('autoCreate'))) {
            $autoCreate = filter_var($params['autoCreate'], FILTER_VALIDATE_INT);
        }

        $qb = $siteRepository->getSitesForUserWithPagination($this->getUser(), $this->getParams(), $autoCreate);
        return $this->getPagination($qb, $request);
    }

    /**
     * Get all sites not belong to any SubPublisher of a Publisher
     *
     * @Rest\Get("/sites/notBelongToSubPublisher")
     *
     * @Rest\View(
     *      serializerGroups={"site.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return SiteInterface[]
     */
    public function getSitesNotBelongToSubPublisherAction()
    {
        $user = $this->getUser();

        if ($user instanceof AdminInterface || $user instanceof SubPublisherInterface) {
            return $this->view('Permission decline, only supported for Publisher', Codes::HTTP_FORBIDDEN);
        }

        return $this->get('tagcade.repository.site')->getSitesNotBelongToSubPublisherForPublisher($user);
    }

    /**
     * Get a single site for the given id
     *
     * @Rest\Get("/sites/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary", "publisherexchange.summary", "exchange.summary"}
     * )
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\SiteInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PUBLISHER')")
     *
     * Create a site from the submitted data
     *
     * @ApiDoc(
     *  section="Sites",
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
        try {
            return $this->post($request);
        }
        catch (\Exception $e) {
            return $this->view('Could not create site by server error or may be this site domain have been created previously', Codes::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update an existing site from the submitted data or create a new site
     *
     * @ApiDoc(
     *  section="Sites",
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
     * Update an existing site from the submitted data or create a new site at a specific location
     *
     * @ApiDoc(
     *  section="Sites",
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
        /**
         * @var SiteInterface $site
         */
        $site = $this->one($id);

        if(array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if($site->getPublisherId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing site
     *
     * @ApiDoc(
     *  section="Sites",
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

    /**
     * Delete one channel in channels list for a existing site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @Rest\Delete("/sites/{siteId}/channel/{channelId}", requirements={"siteId" = "\d+", "channelId" = "\d+"})
     *
     * @param int $siteId the site id
     * @param int $channelId the channel id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteChannelForSiteAction($siteId, $channelId)
    {
        /** @var SiteInterface $site */
        $site = $this->getOr404($siteId);

        $this->checkUserPermission($site, 'edit');

        $result = $this->get('tagcade.domain_manager.site')->deleteChannelForSite($site, $channelId);

        return $this->view(null, ($result > 0 ? Codes::HTTP_NO_CONTENT : Codes::HTTP_NOT_FOUND));
    }

    /**
     * Retrieve a list of ad slots for this site
     * @Rest\View(
     *      serializerGroups={"user.min", "slotlib.summary", "adslot.detail", "displayadslot.summary", "librarydisplayadslot.summary", "librarynativeadslot.summary", "librarydynamicadslot.summary", "nativeadslot.summary", "dynamicadslot.summary", "expression.detail", "libraryexpression.detail", "site.summary"}
     * )
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return \Tagcade\Model\Core\BaseAdSlotInterface[]
     */
    public function getAdslotsAction($id, Request $request)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        if ($request->query->get('page') > 0) {
            $adSlotRepository = $this->get('tagcade.repository.ad_slot');
            $qb = $adSlotRepository->getAdSlotsForSiteWithPagination($site, $this->getParams());
            return $this->getPagination($qb, $request);
        }
        return $this->get('tagcade.domain_manager.ad_slot')
            ->getAdSlotsForSite($site);
    }

    /**
     *
     * Retrieve a list of display ad slots for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\Core\DisplayAdSlotInterface[]
     */
    public function getDisplayadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.display_ad_slot')
            ->getAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of dynamic ad slots for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param int $id
     * @return \Tagcade\Model\Core\DynamicAdSlotInterface[]
     */
    public function getDynamicadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.dynamic_ad_slot')
            ->getDynamicAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of native ad slots for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param int $id
     * @return \Tagcade\Model\Core\NativeAdSlotInterface[]
     */
    public function getNativeadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.native_ad_slot')
            ->getNativeAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of active ad tags for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param int $id
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsActiveAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForSite($site, true);
    }

    /**
     * Get the javascript display ad tags for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function getJstagsAction(Request $request, $id)
    {
        $params = $request->query->all();
        /** @var SiteInterface $site */
        $site = $this->one($id);

        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }

        return $this->get('tagcade.service.tag_generator')
            ->getTagsForSite($site, $forceSecure);
    }

    /**
     * Get the javascript header of tag for this site
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function getJsheadertagAction(Request $request, $id)
    {
        $params = $request->query->all();
        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }
        /** @var SiteInterface $site */
        $site = $this->one($id);

        if ($this->getUser() instanceof PublisherInterface && !$site->getPublisher()->hasAnalyticsModule()) {
            throw new BadRequestHttpException('That publisher is not enabled Analytics module');
        }

        return $this->get('tagcade.service.tag_generator')
            ->getHeaderForSite($site, $forceSecure);
    }

    /**
     * Retrieve a list of channels for this site
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param int $id
     * @return \Tagcade\Model\Core\ChannelInterface[]
     */
    public function getChannelsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $site->getChannels();
    }

    /**
     * get all Sites which have no Ad Slot references to a library Ad Slot
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary"}
     * )
     *
     * @Rest\Get("sites/nodeployments")
     *
     * @Rest\QueryParam(name="slotLibrary", requirements="\d+")
     *
     * @ApiDoc(
     *  section="Sites",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @return array
     */
    public function getSitesUnreferencedToLibraryAdSlotAction()
    {
        /** @var ParamFetcherInterface $paramFetcher */
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $slotLibraryId = $paramFetcher->get('slotLibrary');
        $slotLibraryId = filter_var($slotLibraryId, FILTER_VALIDATE_INT);

        $slotLibrary = $this->get('tagcade.domain_manager.library_ad_slot')->find($slotLibraryId);
        if (!$slotLibrary instanceof BaseLibraryAdSlotInterface) {
            throw new NotFoundHttpException('Not found that slot library');
        }

        $this->checkUserPermission($slotLibrary, 'edit');

        return $this->get('tagcade.domain_manager.site')->getSitesUnreferencedToLibraryAdSlot($slotLibrary);
    }

    protected function getResourceName()
    {
        return 'site';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_site';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.site');
    }
}
