<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\AdminApiBundle\Event\HandlerEventLog;
use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\DisplayBlacklistRepositoryInterface;
use Tagcade\Repository\Core\DisplayWhiteListRepositoryInterface;


/**
 * @Rest\RouteResource("Adnetwork")
 */
class AdNetworkController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad networks
     *
     * @Rest\View(serializerGroups={"adnetwork.extra", "user.min", "adtag.summary", "partner.summary", "display.blacklist.summary", "display.blacklist.min", "network.blacklist.summary"})
     *
     * @Rest\QueryParam(name="builtIn", nullable=true, requirements="true|false", description="get built-in ad network or not")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="publisherId", nullable=true, description="the publisher id which is used for filtering sites")
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $request
     * @return AdNetworkInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        /** @var AdNetworkRepositoryInterface $adNetworkRepository */
        $adNetworkRepository = $this->get('tagcade.repository.ad_network');
        if ($request->query->count() < 1) {
            if ($role instanceof PublisherInterface) {
                return $adNetworkRepository->getAdNetworksForPublisher($role);
            }

            return $this->all();
        }

        $builtIn = null;
        if (is_string($request->query->get('autoCreate'))) {
            $builtIn = filter_var($params['autoCreate'], FILTER_VALIDATE_BOOLEAN);
        }

        $qb = $adNetworkRepository->getAdNetworksForUserWithPagination($this->getUser(), $this->getParams(), $builtIn);
        return $this->getPagination($qb, $request);

//        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
//        $publisher = $paramFetcher->get('publisher');
//        $adNetworkManager = $this->get('tagcade.domain_manager.ad_network');
//        $builtIn = $paramFetcher->get('builtIn');
//        $builtIn = filter_var($builtIn, FILTER_VALIDATE_BOOLEAN);
//
//        if ($publisher != null && $this->getUser() instanceof AdminInterface) {
//            $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisher);
//
//            if (!$publisher instanceof PublisherInterface) {
//                throw new NotFoundHttpException('That publisher does not exist');
//            }
//
//            $all = $adNetworkManager->getAdNetworksForPublisher($publisher);
//        }
//
//        $all = isset($all) ? $all : $this->all();
//
//        $this->checkUserPermission($all);
//
//
//        if ($builtIn == false) {
//            return $all;
//        }
//
//        $results = [];
//        foreach ($all as $adNetwork) {
//            /**
//             * @var AdNetworkInterface $adNetwork
//             */
//            if (!$adNetwork->getNetworkPartner() instanceof AdNetworkPartnerInterface) {
//                continue;
//            }
//
//            $results[] = $adNetwork;
//        }
//
//        return $results;
    }

    /**
     * Get a single ad network for the given id
     *
     * @Rest\View(serializerGroups={"adnetwork.extra", "user.summary", "adtag.summary", "partner.summary", "display.blacklist.summary", "network.blacklist.summary"})
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\AdNetworkInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * @Rest\Get("/adnetworks/{cname}/publishers")
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     *
     * @Rest\View(serializerGroups={"adnetwork.credential", "user.uuid", "adtag.summary", "partner.summary", "display.blacklist.summary"})
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param string $cname partner's canonical name
     *
     * @return \Tagcade\Model\Core\AdNetworkInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getPublishersByCNameAction($cname, Request $request)
    {
        $publisherId = $request->query->get('publisher', null);

        return $this->get('tagcade.repository.ad_network')->getPartnerConfigurationForAllPublishers($cname, $publisherId);
    }

    /**
     * Get all sites belonging to this ad network
     *
     * @Rest\View(serializerGroups={"sitestatus.detail", "site.minimum"})
     * @Rest\QueryParam(name="publisher", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="size", requirements="\d+", nullable=true)
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return SiteInterface[]
     *
     */
    public function getSitesAction($id, Request $request)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->one($id);

        // publisher for filter
        $user = $this->getUser();

        /* NOTE: default publisher-filter = no filter if admin, or this $user if publisher */
        $publisher = $user instanceof AdminInterface ? null : $user;

        /* only check permission if has query param "publisher" */
        if ($request->query->has('publisher')) {
            $publisherId = $request->query->get('publisher', null);
            $publisher = $this->getPublisher($publisherId);
        }

        $page = $request->query->get('page', null);

        if ($page === null) {
            return $this->get('tagcade_app.service.core.ad_network.ad_network_service')->getSitesForAdNetworkFilterPublisher($adNetwork, $publisher);
        }

        $size = $request->query->get('size', 10);
        $offset = ($page - 1) * $size;
        $siteStatus = $this->get('tagcade_app.service.core.ad_network.ad_network_service')->getSitesForAdNetworkFilterPublisher($adNetwork, $publisher);

        return array (
            'totalRecord' => count($siteStatus),
            'records' => array_slice($siteStatus, $offset, $size),
            'itemPerPage' => $size,
            'currentPage' => $page
        );
    }


    /**
     * Get all active ad tags belonging to this ad network and publisher
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @return AdTagInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAdtagsActiveAction($id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        return $this->get('tagcade.domain_manager.ad_tag')->getAdTagsForAdNetworkFilterPublisher($adNetwork);
    }

    /**
     * Get all active sites belonging to this ad network
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @return SiteInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getSitesActiveAction($id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $role = $this->get('tagcade.user_role');

        if ($role instanceof AdminInterface) {
            $siteIds = $this->get('tagcade.repository.ad_tag')->getActiveSitesForAdNetworkFilterPublisher($adNetwork);
        } else {
            /** @var PublisherInterface $role */
            $siteIds = $this->get('tagcade.repository.ad_tag')->getActiveSitesForAdNetworkFilterPublisher($adNetwork, $role);
        }

        $sites = [];
        $siteManager = $this->get('tagcade.domain_manager.site');
        foreach($siteIds as $siteId) {
            $site = $siteManager->find($siteId);
            if ($site instanceof SiteInterface) {
                $sites[] = $site;
            }
        }

        return $sites;
    }

    /**
     * Get all active ad tags belonging to this ad network and site filter publisher
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @param $siteId
     * @return AdTagInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getSiteAdtagsActiveAction($id, $siteId)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        /** @var SiteInterface $site */
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork) || false === $this->get('security.context')->isGranted('edit', $site)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        return $this->get('tagcade.domain_manager.ad_tag')->getAdTagsForAdNetworkAndSiteFilterPublisher($adNetwork, $site);
    }

    /**
     *
     * Get all active ad tags belonging to this ad network
     *
     * @Rest\View(
     *      serializerGroups={"adtag.detail", "adslot.summary", "displayadslot.summary", "nativeadslot.summary", "slotlib.summary", "librarynativeadslot.summary", "librarydisplayadslot.summary", "site.summary", "libraryadtag.detail", "display.blacklist.summary"}
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
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     *
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsAction(Request $request, $id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->one($id);
        $adTagRepository = $this->get('tagcade.repository.ad_tag');
        if ($request->query->get('page') > 0) {
            $qb = $adTagRepository->getAdTagsForAdNetworkWithPagination($adNetwork, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $adTagRepository->getAdTagsForAdNetwork($adNetwork);
    }

    /**
     * @Rest\QueryParam(name="resetToken", requirements="true|false", nullable=true)
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @param $id
     * @return mixed|string
     */
    public function getEmailtokenAction($id)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $resetToken = $paramFetcher->get('resetToken');
        $resetToken = filter_var($resetToken, FILTER_VALIDATE_BOOLEAN);

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->one($id);

        if (!$adNetwork->getNetworkPartner() instanceof AdNetworkPartnerInterface) {
            throw new InvalidArgumentException('This AdNetwork does not have any Partner');
        }

        $this->checkUserPermission($adNetwork, 'edit');

        return $this->get('tagcade.domain_manager.ad_network')->getUnifiedReportEmail($adNetwork, $resetToken);
    }

    /**
     * Retrieve a list of displayBlacklist for this adNetwork
     *
     * @Rest\View(
     *      serializerGroups={"adnetwork.summary", "user.summary", "display.blacklist.summary", "network.blacklist.min"}
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
     *  section="AdNetworks",
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
     * @return \Tagcade\Model\Core\DisplayBlacklistInterface[]
     */
    public function getDisplayblacklistsAction(Request $request, $id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->one($id);

        /** @var DisplayBlacklistRepositoryInterface $displayBlacklistRepository */
        $displayBlacklistRepository = $this->get('tagcade.repository.display.blacklist');


        if ($request->query->get('page') > 0) {
            $qb = $displayBlacklistRepository->getDisplayBlacklistsForAdNetworkWithPagination($adNetwork, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $displayBlacklistRepository->getBlacklistsForAdNetwork($adNetwork);
    }

    /**
     * Retrieve a list of displayWhiteList for this adNetwork
     *
     * @Rest\View(
     *      serializerGroups={"adnetwork.summary", "user.summary", "display.whitelist.summary", "network.whitelist.min"}
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
     *  section="AdNetworks",
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
     * @return \Tagcade\Model\Core\DisplayWhiteListInterface[]
     */
    public function getDisplaywhitelistsAction(Request $request, $id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->one($id);

        /** @var DisplayWhiteListRepositoryInterface $displayWhiteListRepository */
        $displayWhiteListRepository = $this->get('tagcade.repository.display.white_list');


        if ($request->query->get('page') > 0) {
            $qb = $displayWhiteListRepository->getDisplayWhiteListsForAdNetworkWithPagination($adNetwork, $this->getParams());

            return $this->getPagination($qb, $request);
        }

        return $displayWhiteListRepository->getWhiteListsForAdNetwork($adNetwork);
    }


    /**
     * Create a ad network from the submitted data
     *
     * @ApiDoc(
     *  section = "Ad Networks",
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
     *
     * validate if the [token] belong to the combination of publisherId and partnerCName
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     * @param Request $request
     * @return boolean
     */
    public function postTokenvalidationAction(Request $request)
    {
        $parameters = $request->request->all();
        if (!isset($parameters['publisherId']) || !isset($parameters['token']) || !isset($parameters['partnerCName'])) {
            throw new InvalidArgumentException('either publisherId or token or partnerCName is missing');
        }

        return $this->get('tagcade.domain_manager.ad_network')->validateEmailHookToken($parameters['publisherId'], $parameters['partnerCName'], $parameters['token']);
    }

    /**
     * Update ad tag status to active/paused for an ad network
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @Rest\QueryParam(name="active", requirements="(true|false)", nullable=true)
     *
     * @param $id
     * @return View|FormTypeInterface
     */
    public function putStatusAction($id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->getOr404($id);

        $this->checkUserPermission($adNetwork, 'edit');
        /** @var ParamFetcherInterface $paramFetcher */
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $active = $paramFetcher->get('active');
        $active = filter_var($active, FILTER_VALIDATE_BOOLEAN);

        $this->get('tagcade.worker.manager')->updateAdTagStatusForAdNetwork($id, $active);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * refresh email hook token for a single ad network
     *
     * @param $id
     * @return View
     */
    public function putEmailtokenAction($id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->getOr404($id);

        $this->checkUserPermission($adNetwork, 'edit');
        //refresh token
        $adNetwork->setEmailHookToken(uniqid(''));
        $this->get('tagcade.domain_manager.ad_network')->save($adNetwork);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update an existing ad network from the submitted data or create a new ad network
     *
     * @ApiDoc(
     *  section = "Ad Networks",
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
     *
     *
     * Update revenue for ad network.
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", description="Cpm rate of ad network")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return View
     */
    public function putEstcpmAction($id)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        //check param estCpm is number?
        $estCpmParam = $paramFetcher->get('estCpm');
        if (!is_numeric($estCpmParam)) {
            throw new InvalidArgumentException('estCpm should be numeric');
        }

        $estCpm = (float)$estCpmParam;
        if ($estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be positive value');
        }

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.worker.manager')->updateRevenueForAdNetwork($adNetwork, $estCpm, $startDate, $endDate);

        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
        $event = new HandlerEventLog('PUT', $adNetwork);
        $event->addChangedFields('estCpm', '', $estCpm, $startDate, $endDate);
        /** @var AdTagInterface[] $adTags */
        $adTags = $adNetwork->getAdTags();
        foreach ($adTags as $adTag) {
            $event->addAffectedEntityByObject($adTag);
        }
        $this->getHandler()->dispatchEvent($event);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     *
     *
     * Update revenue for ad network and site.
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", description="Cpm rate of ad network")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     * @param $siteId
     *
     * @return View
     */
    public function putSitesEstcpmAction($id, $siteId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        //check param estCpm is number?
        $estCpmParam = $paramFetcher->get('estCpm');
        if (!is_numeric($estCpmParam)) {
            throw new InvalidArgumentException('estCpm should be numeric');
        }

        $estCpm = (float)$estCpmParam;
        if ($estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be positive value');
        }

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        /** @var SiteInterface $site */
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);
        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }


        if (false === $this->get('security.context')->isGranted('edit', $adNetwork) || false === $this->get('security.context')->isGranted('edit', $site)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }


        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.worker.manager')->updateRevenueForAdNetworkAndSite($adNetwork, $site, $estCpm, $startDate, $endDate);

        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
        $event = new HandlerEventLog('PUT', $adNetwork);
        $event->addChangedFields('estCpm', '', $estCpm, $startDate, $endDate);
        $event->addAffectedEntityByObject($site);
        /** @var AdTagInterface[] $adTags */
        $adTags = $adNetwork->getAdTags();
        foreach ($adTags as $adTag) {
            $event->addAffectedEntityByObject($adTag);
        }
        $this->getHandler()->dispatchEvent($event);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update active state of all at tags belonging to adNetwork $id and $siteId
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="active", requirements="\d+", description="active status of site")
     *
     * @param $id
     * @param $siteId
     *
     * @return View
     */
    public function putSiteStatusAction($id, $siteId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

//        //get oldValue for action log bellow. Not clone direct $adNetwork->getAdTags() because lazy load and oldValue then be updated as newValue
//        $adTags = $adNetwork->getAdTags();
//
//        /** @var AdTagInterface[] $adTagsOld */
//        $adTagsOld = [];
//
//        foreach ($adTags as $adTag) {
//            $adTagsOld[] = clone $adTag;
//        }

        /** @var SiteInterface $site */
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);
        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork) || false === $this->get('security.context')->isGranted('edit', $site)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $active = $paramFetcher->get('active', true) != 0 ? true : false;

        $this->get('tagcade.worker.manager')->updateAdTagStatusForAdNetwork($id, $active, $siteId);

//        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
//        $event = new HandlerEventLog('PUT', $adNetwork);
//
//        ////add Affected for Site
//        $event->addAffectedEntityByObject($site);
//
//        ////detect and add Affected for AdTags
//        $hasAdTagAffected = false;
//        foreach ($adTagsOld as $adTag) {
//            //only add ad tag to affected list if really has changing
//            if ($adTag->getAdSlot()->getSite() == $site && $active != $adTag->isActive()) {
//                $event->addAffectedEntityByObject($adTag);
//                $hasAdTagAffected = true;
//            }
//        }
//
//        ////add ChangedFields: if really has changing, 'active' changed from !$active to $active, then dispatch Event Log
//        if ($hasAdTagAffected) {
//            $event->addChangedFields('active', !$active, $active);
//
//            ////dispatch Event Log
//            $this->getHandler()->dispatchEvent($event);
//        }

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update position of of all ad tags belonging to adNetwork,
     * support auto shift down all ad tags of other ad network with positions greater than or equal
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="position", requirements="\d+", description="new position of all ad tags belonging to this ad network")
     * @Rest\QueryParam(name="autoIncreasePosition", requirements="true|false", default="false", nullable=true, description="auto shift down all ad tags that belonging to other ad network and have position greater than or equal")
     *
     * @param $id
     *
     * @return View
     */
    public function putPositionAction($id)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $this->checkUserPermission($adNetwork, 'edit');

        return $this->cascadePosition($adNetwork);
    }

    /**
     * Update position of of all ad tags belonging to adNetwork and filtered by site
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="position", requirements="\d+", description="new position of all ad tags belonging to this ad network and filtered by siteId")
     * @Rest\QueryParam(name="autoIncreasePosition", requirements="true|false", default="false", nullable=true, description="auto shift down all ad tags that belonging to other ad network and have position greater than or equal")
     *
     * @param $id
     * @param $siteId
     *
     * @return View
     */
    public function putSitePositionAction($id, $siteId)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $this->checkUserPermission($adNetwork, 'edit');

        /** @var SiteInterface $site */
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);
        if (!$site instanceof SiteInterface) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $this->cascadePosition($adNetwork, $site);
    }

    /**
     * Update position of of all ad tags belonging to adNetwork and filtered by channel
     *
     * @ApiDoc(
     *  section = "Ad Networks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="position", requirements="\d+", description="new position of all ad tags belonging to this ad network and filtered by $channelId")
     * @Rest\QueryParam(name="autoIncreasePosition", requirements="true|false", default="false", nullable=true, description="auto shift down all ad tags that belonging to other ad network and have position greater than or equal")
     *
     * @param $id
     * @param $channelId
     *
     * @return View
     */
    public function putChannelPositionAction($id, $channelId)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $this->checkUserPermission($adNetwork, 'edit');

        /** @var ChannelInterface $channel */
        $channel = $this->get('tagcade.domain_manager.channel')->find($channelId);
        if (!$channel instanceof ChannelInterface) {
            throw new NotFoundHttpException('That channel does not exist');
        }

        $this->checkUserPermission($channel);

        return $this->cascadePosition($adNetwork, $channel->getSites());
    }

    /**
     * Update an existing ad network from the submitted data or create a new ad network at a specific location
     *
     * @ApiDoc(
     *  section = "Ad Networks",
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
         * @var AdNetworkInterface $adNetwork
         */
        $adNetwork = $this->one($id);

        if (array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if ($adNetwork->getPublisherId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing ad network
     *
     * @ApiDoc(
     *  section = "Ad Networks",
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
     * cascade Position for an ad network, filter by site(s) (optional)
     *
     * @param AdNetworkInterface $adNetwork
     * @param null|SiteInterface|SiteInterface[] $sites
     * @return View
     */
    private function cascadePosition(AdNetworkInterface $adNetwork, $sites = null)
    {
        // get position from request query params
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $position = (int)$paramFetcher->get('position', true);
        $autoIncreasePosition = $paramFetcher->get('autoIncreasePosition') == 'true';

        if ($position < 1) {
            return $this->view('position should be greater than zero', Codes::HTTP_BAD_REQUEST);
        }

        // do cascading position by worker background process
        $this->get('tagcade.worker.manager')->updateAdTagPositionForAdNetworkAndSites($adNetwork, $position, $sites, $autoIncreasePosition);

//        // now dispatch a HandlerEventLog for handling event, for example ActionLog handler...
//        $event = new HandlerEventLog('PUT', $adNetwork);
//
//        $event->addChangedFields('position', '', $position);
//
//        /** @var AdTagInterface[] $adTags */
//        $adTags = $adNetwork->getAdTags();
//        foreach ($adTags as $adTag) {
//            $event->addAffectedEntityByObject($adTag);
//        }
//
//        $this->getHandler()->dispatchEvent($event);

        // return view
        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * @param int $publisherId
     * @return PublisherInterface
     * @throws LogicException
     */
    private function getPublisher($publisherId)
    {
        /** @var PublisherInterface $publisher */
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            // try again with SubPublisher
            $publisher = $this->get('tagcade_user_system_sub_publisher.user_manager')->findUserBy(array('id' => $publisherId));
        }

        if (!$publisher instanceof PublisherInterface) {
            throw new LogicException('The user should have the publisher role');
        }

        $this->checkUserPermission($publisher);

        return $publisher;
    }

    protected function getLogger()
    {
        return $this->get('logger');
    }

    protected function getResourceName()
    {
        return 'adnetwork';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_adnetwork';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_network');
    }
}
