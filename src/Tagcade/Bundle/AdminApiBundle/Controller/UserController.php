<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Tagcade\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class UserController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all publisher
     * @Rest\View(serializerGroups={"user.detail","user.billing"})
     * @Rest\Get("/users")
     * @Rest\QueryParam(name="all", requirements="(true|false)", nullable=true)
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User[]
     */
    public function cgetAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $all = $paramFetcher->get('all');

        if ($all === null || !filter_var($all, FILTER_VALIDATE_BOOLEAN)) {
            return $this->getHandler()->allActivePublishers();
        }

        return $this->getHandler()->allPublishers();
    }

    /**
     * Get a single publisher for the given id
     * @Rest\View(serializerGroups={"user.detail", "user.billing", "billingConfigs.detail","billingConfigs.summary"})
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Bundle\UserBundle\Entity\User
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get the javascript display ad tags for all ron ad slot of this publisher
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id
     * @return array
     */
    public function getRonjstagsAction($id)
    {
        /** @var PublisherInterface $publisher */
        $publisher = $this->one($id);

        return $this->get('tagcade.service.tag_generator')
            ->getRonTagsForPublisher($publisher);
    }

    /**
     * Get js passback
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function getJspassbackAction(Request $request, $id)
    {
        $params = $request->query->all();
        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }

        /** @var PublisherInterface $publisher */
        $publisher = $this->one($id);
        return $this->get('tagcade.service.tag_generator')->getTagsForPassback($publisher, $forceSecure);
    }

    /**
     * get header of tag for publisher
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $id
     * @return array
     */
    public function getJsheadertagAction(Request $request, $id)
    {
        $params = $request->query->all();
        $forceSecure = false;
        if (array_key_exists('forceSecure', $params)) {
            $forceSecure = filter_var($params['forceSecure'],FILTER_VALIDATE_BOOLEAN);
        }

        /** @var PublisherInterface $publisher */
        $publisher = $this->one($id);

        return $this->get('tagcade.service.tag_generator')->getHeaderForPublisher($publisher, $forceSecure);
    }

    /**
     * Get ad networks for publisher
     *
     * @Rest\QueryParam(name="builtIn", nullable=true, requirements="true|false", description="get built-in ad network or not")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @Rest\View(serializerGroups={"adnetwork.extra", "user.min", "adtag.summary", "partner.summary"})
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $publisherId
     * @return array|\Tagcade\Model\Core\AdNetworkInterface[]
     */
    public function getAdnetworksAction(Request $request, $publisherId)
    {
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');

        $publisher = $publisherManager->findPublisher($publisherId);
        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        /** @var AdNetworkRepositoryInterface $adNetworkRepository */
        $adNetworkRepository = $this->get('tagcade.repository.ad_network');
        if ($request->query->count() < 1) {
            return $adNetworkRepository->getAdNetworksForPublisher($publisher);
        }

        $builtIn = null;
        if (is_string($request->query->get('autoCreate'))) {
            $builtIn = filter_var($params['autoCreate'], FILTER_VALIDATE_BOOLEAN);
        }

        $qb = $adNetworkRepository->getAdNetworksForUserWithPagination($this->getUser(), $this->getParams(), $builtIn);
        return $this->getPagination($qb, $request);
    }

    /**
     * Get sites with option enable source report for publisher
     *
     * @Rest\QueryParam(name="autoCreate", nullable=true)
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="enableSourceReport", requirements="(true|false)", nullable=true)
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.min", "publisherexchange.summary", "exchange.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $publisherId
     * @return SiteInterface[]
     * @throws NotFoundHttpException
     */
    public function getSitesAction(Request $request, $publisherId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $enableSourceReport = $paramFetcher->get('enableSourceReport');

        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
        $publisher = $publisherManager->findPublisher((int)$publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        $siteManager = $this->get('tagcade.domain_manager.site');

        if ($request->query->count() < 1) {
            if (null !== $enableSourceReport) {
                if (!$publisher->hasAnalyticsModule()) {
                    throw new NotFoundHttpException('That publisher does not have analytics module enabled');
                }

                $enableSourceReport = $enableSourceReport ? filter_var($enableSourceReport, FILTER_VALIDATE_BOOLEAN) : true;
                return $siteManager->getSitesThatEnableSourceReportForPublisher($publisher, $enableSourceReport);
            }

            return $siteManager->getSitesForPublisher($publisher);
        }

        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        /** @var SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->get('tagcade.repository.site');
        $autoCreate = null;

        if (is_string($request->query->get('autoCreate'))) {
            $autoCreate = filter_var($params['autoCreate'], FILTER_VALIDATE_INT);
        }

        if (null !== $enableSourceReport) {
            if (!$publisher->hasAnalyticsModule()) {
                throw new NotFoundHttpException('That publisher does not have analytics module enabled');
            }

            $enableSourceReport = $enableSourceReport ? filter_var($enableSourceReport, FILTER_VALIDATE_BOOLEAN) : true;
        }

        $qb = $siteRepository->getSitesForUserWithPagination($publisher, $this->getParams(), $autoCreate, $enableSourceReport);
        return $this->getPagination($qb, $request);
    }

    /**
     * Get token for publisher only
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $publisherId
     * @return array
     */
    public function getTokenAction($publisherId)
    {
        /**
         * @var PublisherManagerInterface $publisherManager
         */
        $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
        $publisher = $publisherManager->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $jwtTransformer = $this->get('tagcade_api.service.jwt_response_transformer');

        $tokenString = $jwtManager->create($publisher);

        return $jwtTransformer->transform(['token' => $tokenString], $publisher);
    }

    /**
     * Create a user from the submitted data
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true},
     *      {"name"="email", "dataType"="string", "required"=false},
     *      {"name"="plainPassword", "dataType"="string", "required"=true},
     *      {"name"="role", "dataType"="string", "required"=true, "default"="publisher", "description"="The role of the user, i.e publisher or admin"},
     *      {"name"="features", "dataType"="array", "required"=false, "description"="An array of enabled features for this user, not applicable to admins"},
     *      {"name"="enabled", "dataType"="boolean", "required"=false, "description"="Is this user account enabled or not?"},
     *  },
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
     * Update an existing user from the submitted data or create a new publisher
     *
     * @ApiDoc(
     *  section = "admin",
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
     * Update an existing user from the submitted data or create a new publisher at a specific location
     *
     * @ApiDoc(
     *  section = "admin",
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
        return $this->patch($request, $id);
    }

    /**
     * Delete an existing publisher
     *
     * @ApiDoc(
     *  section = "admin",
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
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'admin_api_1_get_user';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_admin_api.handler.user');
    }
}
