<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Entity\Core\AdNetwork;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;

/**
 * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_UNIFIED_REPORT') and has_role('MODULE_DISPLAY'))")
 *
 * Only allow admins and publishers with the display module enabled
 */
class UnifiedReportController extends FOSRestController
{
    const ALL_SITES = null;
    const ALL_AD_TAGS = null;
    const ALL_DEMAND_PARTNER = null;

    const PUBLISHER_KEY = 'publisher';
    const AD_NETWORK_KEY = 'adNetwork';

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/all/partners", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAllPartnersByPartnerReportsAction($publisherId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId);
        /**
         * @var PublisherInterface $publisher
         */
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        //Get all partner for this publisher
        $adNetworks = $this->get('tagcade.domain_manager.ad_network')->getAdNetworksForPublisher($publisher);
        $this->checkUserPermissionByVoter($adNetworks);

        return $this->getResult(
            $this->getReportBuilder()->getAllDemandPartnersByPartnerReport(
                $publisher,
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAllPartnersByDayReportsAction($publisherId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId);
        $publisher = $result[self::PUBLISHER_KEY];

        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getAllDemandPartnersByDayReport(
                $publisher,
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/all/sites", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAllPartnersBySitesReportsAction($publisherId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId);
        $publisher =  $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getAllDemandPartnersBySiteReport(
                $publisher,
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/all/adtags", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAllPartnersByAdTagsReportsAction($publisherId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId);
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getAllDemandPartnersByAdTagReport(
                $result[self::PUBLISHER_KEY],
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @return array
     */
    public function getPartnerAllSitesByDayAction($publisherId, $adNetworkId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];

        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        if ($publisher instanceof SubPublisherInterface) {
            return $this->getResult(
                $this->getReportBuilder()->getPartnerAllSitesByDayForSubPublisherReport(
                    $publisher,
                    $result[self::AD_NETWORK_KEY],
                    $this->getParams()
                )
            );
        }

        return $this->getResult(
            $this->getReportBuilder()->getPartnerAllSitesByDayReport(
                $result[self::AD_NETWORK_KEY],
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites/all/sites", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @return array
     */
    public function getPartnerAllSiteBySiteAction($publisherId, $adNetworkId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPartnerAllSitesBySitesReport(
                $publisher,
                $result[self::AD_NETWORK_KEY],
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites/all/adtags", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @return array
     */
    public function getPartnerAllSitesByAdTagAction($publisherId, $adNetworkId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        //Get all partner for this publisher
        $adNetworks = $this->get('tagcade.domain_manager.ad_network')->getAdNetworksForPublisher($publisher);
        $this->checkUserPermissionByVoter($adNetworks);

        if ($publisher instanceof SubPublisherInterface) {
            return $this->getResult(
                $this->getReportBuilder()->getPartnerByAdTagsForSubPublisherReport(
                    $publisher,
                    $result[self::AD_NETWORK_KEY],
                    $this->getParams()
                )
            );
        }

        return $this->getResult(
            $this->getReportBuilder()->getPartnerAllSitesByAdTagsReport(
                $result[self::AD_NETWORK_KEY],
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites/{siteId}", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+", "siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @param int $siteId
     * @return array
     */
    public function getPartnerSiteByDaysReportsAction($publisherId, $adNetworkId, $siteId)
    {
        $site = $this->get('tagcade.repository.site')->find($siteId);
        if (!$site instanceof SiteInterface) {
            throw new NotFoundHttpException(sprintf('site %d not found', $siteId));
        }

        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];

        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        if (!$publisher instanceof SubPublisherInterface && $site->getPublisherId() !== $publisher->getId()) {
            throw new AccessDeniedException('you do not have enough permission to view this entity');
        }


        if ($publisher instanceof SubPublisherInterface &&
            (
                ($site->getSubPublisher() instanceof SubPublisherInterface && $site->getSubPublisher()->getId() !== $publisher->getId()) ||
                !$site->getSubPublisher() instanceof SubPublisherInterface
            )
        ) {
            throw new AccessDeniedException('you do not have enough permission to view this entity');
        }

        if ($publisher instanceof SubPublisherInterface) {
            return $this->getResult(
                $this->getReportBuilder()->getPartnerSiteByDaysForSubPublisherReport(
                    $publisher,
                    $result[self::AD_NETWORK_KEY],
                    $site->getDomain(),
                    $this->getParams()
                )
            );
        }

        return $this->getResult(
            $this->getReportBuilder()->getPartnerSiteByDaysReport(
                $result[self::AD_NETWORK_KEY],
                $site->getDomain(),
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites/{siteId}/adtags", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+", "siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @param int $siteId
     * @return array
     */
    public function getPartnerSiteByAdTagsReportsAction($publisherId, $adNetworkId, $siteId)
    {
        $site = $this->get('tagcade.repository.site')->find($siteId);
        if (!$site instanceof SiteInterface) {
            throw new NotFoundHttpException(sprintf('site %d not found', $siteId));
        }

        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        //Get all partner for this publisher
        $adNetworks = $this->get('tagcade.domain_manager.ad_network')->getAdNetworksForPublisher($publisher);
        $this->checkUserPermissionByVoter($adNetworks);

        if (!$publisher instanceof SubPublisherInterface && $site->getPublisherId() !== $publisher->getId()) {
            throw new AccessDeniedException('you do not have enough permission to view this entity');
        }

        if ($publisher instanceof SubPublisherInterface &&
            (
                ($site->getSubPublisher() instanceof SubPublisherInterface && $site->getSubPublisher()->getId() !== $publisher->getId()) ||
                !$site->getSubPublisher() instanceof SubPublisherInterface
            )
        ) {
            throw new AccessDeniedException('you do not have enough permission to view this entity');
        }

        if ($publisher instanceof SubPublisherInterface) {
            return $this->getResult(
                $this->getReportBuilder()->getPartnerSiteByAdTagsForSubPublisherReport(
                    $publisher,
                    $result[self::AD_NETWORK_KEY],
                    $site,
                    $this->getParams()
                )
            );
        }

        return $this->getResult(
            $this->getReportBuilder()->getPartnerSiteByAdTagsReport(
                $result[self::AD_NETWORK_KEY],
                $site,
                $this->getParams()
            )
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/all/subpublishers", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getAllPartnersBySubPublishersReportAction($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Tagcade\Exception\InvalidArgumentException(sprintf('the publisher %d does not exist', $publisherId));
        }

        if (!$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getReportBuilder()->getSubPublishersReport($publisher, $this->getParams());
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/partners/{adNetworkId}/sites/all/subpublishers", requirements={"publisherId" = "\d+", "adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=false)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="expand", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @param int $adNetworkId
     * @return array
     */
    public function getPartnerBySubPublishersReportAction($publisherId, $adNetworkId)
    {
        $result = $this->verifiedUserPermission($this->getUser(), $publisherId, $adNetworkId);
        $publisher = $result[self::PUBLISHER_KEY];
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('you do not have enough permission to view this report');
        }
        
        if ($publisher instanceof PublisherInterface && !$publisher instanceof SubPublisherInterface && !$publisher->hasUnifiedReportModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getReportBuilder()->getSubPublishersReportByPartner(
            $result[self::AD_NETWORK_KEY],
            $publisher,
            $this->getParams()
        );
    }

    /* all private functions */
    private function verifiedUserPermission(UserRoleInterface $user, $publisherId, $adNetworkId = null)
    {
        $adNetwork = null;

        if (null !== $adNetworkId) {
            $adNetwork = $this->get('tagcade.repository.ad_network')->find($adNetworkId);
            if (!$adNetwork instanceof AdNetwork) {
                throw new NotFoundHttpException('Not found that AdNetwork Partner');
            }
        }

        $publisher = $this->get('tagcade_user.domain_manager.sub_publisher')->find($publisherId);
        if ($publisher instanceof SubPublisherInterface) {

            if ($user instanceof SubPublisherInterface && $user->getId() !== $publisher->getId()) {
                throw new AccessDeniedException('You do not have enough permission to view this entity');
            }

            return array (
                self::PUBLISHER_KEY => $publisher,
                self::AD_NETWORK_KEY => null !== $adNetwork ? $adNetwork : null
            );

        }

        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException('Not found that Publisher');
        }

        if ($user instanceof PublisherInterface && $user->getId() !== $publisher->getId()) {
            throw new AccessDeniedException('You do not have enough permission to view this entity');
        }

        return array (
            self::PUBLISHER_KEY => $publisher,
            self::AD_NETWORK_KEY => null !== $adNetwork ? $adNetwork : null
        );
    }

    /**
     * @return Params
     */
    private function getParams()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        return $this->_createParams($params);
    }

    /**
     * @var array $params
     * @return Params
     */
    private function _createParams(array $params)
    {
        // create a params array with all values set to null
        $defaultParams = array_fill_keys([
            Params::PARAM_START_DATE,
            Params::PARAM_END_DATE,
            Params::PARAM_EXPAND,
            Params::PARAM_GROUP
        ], null);

        $params = array_merge($defaultParams, $params);

        $dateUtil = $this->get('tagcade.service.date_util');
        $startDate = $dateUtil->getDateTime($params[Params::PARAM_START_DATE], true);
        $endDate = $dateUtil->getDateTime($params[Params::PARAM_END_DATE]);

        $expanded = filter_var($params[Params::PARAM_EXPAND], FILTER_VALIDATE_BOOLEAN);
        $grouped = filter_var($params[Params::PARAM_GROUP], FILTER_VALIDATE_BOOLEAN);

        return new Params($startDate, $endDate, $expanded, $grouped);
    }

    /**
     * get Result
     * @param $result
     * @return mixed
     * @throws NotFoundHttpException
     */
    private function getResult($result)
    {
        if ($result === false
            || (is_array($result) && count($result) < 1)
        ) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
    }

    /**
     * @return \Tagcade\Service\Report\UnifiedReport\Selector\ReportBuilderInterface
     */
    private function getReportBuilder()
    {
        return $this->get('tagcade.service.report.unified_report.selector.report_builder');
    }

    /**
     * @param ModelInterface|ModelInterface[] $entity The entity instance
     * @param string $permission
     * @return bool
     * @throws InvalidArgumentException if you pass an unknown permission
     * @throws AccessDeniedException
     */
    protected function checkUserPermissionByVoter($entity, $permission = 'view')
    {
        $toCheckEntities = [];
        if ($entity instanceof ModelInterface) {
            $toCheckEntities[] = $entity;
        }
        else if (is_array($entity)) {
            $toCheckEntities = $entity;
        }
        else {
            throw new \InvalidArgumentException('Expect argument to be ModelInterface or array of ModelInterface');
        }

        foreach ($toCheckEntities as $item) {
            if (!$item instanceof ModelInterface) {
                throw new \InvalidArgumentException('Expect Entity Object and implement ModelInterface');
            }

            $this->checkUserPermissionForSingleEntity($item, $permission);
        }

        return true;
    }

    protected function checkUserPermissionForSingleEntity(ModelInterface $entity, $permission)
    {
        if (!in_array($permission, ['view', 'edit'])) {
            throw new InvalidArgumentException('checking for an invalid permission');
        }

        $securityContext = $this->get('security.context');

        // allow admins to everything
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // check voters
        if (false === $securityContext->isGranted($permission, $entity)) {
            throw new AccessDeniedException(
                sprintf(
                    'You do not have permission to %s this resource',
                    $permission
                )
            );
        }

        return true;
    }
}
