<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use InvalidArgumentException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Params;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\ReportBuilderInterface;

/**
 * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or (has_role('ROLE_SUB_PUBLISHER') and user.isEnableViewTagcadeReport()) ) and has_role('MODULE_DISPLAY'))")
 *
 * Only allow admins and publishers with the display module enabled
 */
class HeaderBiddingReportController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Get("/platform")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)*
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  },
     *  parameters={
     *      {"name"="startDate", "dataType"="datetime", "required"=true, "description"="the start of date period"},
     *      {"name"="endDate", "dataType"="datetime", "required"=true, "description"="the end of date period"},
     *      {"name"="group", "dataType"="boolean", "required"=false, "description"="if group is provided true then all sub reports should be grouped"}
     *  }
     * )
     *
     * @return array
     */
    public function getPlatformAction()
    {
        return $this->getResult(
            $this->getReportBuilder()->getPlatformReport($this->getParams())
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Get("/platform/accounts")
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return array
     */
    public function getPlatformPublishersAction()
    {
        return $this->getResult(
            $this->getReportBuilder()->getAllPublishersReport($this->getParams())
        );
    }

    /**
     * @Security("has_role('ROLE_ADMIN') or ( (has_role('ROLE_PUBLISHER') or has_role('ROLE_SUB_PUBLISHER') ) and has_role('MODULE_DISPLAY'))")
     *
     * @Rest\Get("/accounts/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getPublisherAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        if (!$publisher->hasHeaderBiddingModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPublisherReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/sites/all/sites", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
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
    public function getPublisherSitesBySiteAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        if (!$publisher->hasHeaderBiddingModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPublisherSitesReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/sites/{siteId}", requirements={"siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="the start of date period")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="the end of date period")
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true, description="if group is provided true then all sub reports should be grouped")
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  description = "get performance report for the given {siteId}",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $siteId ID of the site you want the report for
     *
     * @return array
     */
    public function getSiteAction($siteId)
    {
        $site = $this->getSite($siteId);

        $publisher = $site->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getSiteReport($site, $this->getParams())
        );
    }

    /**
     * @param ModelInterface|ModelInterface[] $entity The entity instance
     * @param string $permission
     * @return bool
     * @throws InvalidArgumentException if you pass an unknown permission
     * @throws AccessDeniedException
     */
    protected function checkUserPermission($entity, $permission = 'view')
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
                    'You do not have permission to %s this object or it does not exist',
                    $permission
                )
            );
        }

        return true;
    }

    /**
     * @param $publisherId
     * @return PublisherInterface
     */
    protected function getPublisher($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            // try again with SubPublisher
            throw new LogicException('The user should have the publisher role');
        }

        $this->checkUserPermission($publisher);

        return $publisher;
    }

    /**
     * @param int $siteId
     * @return \Tagcade\Model\Core\SiteInterface
     */
    protected function getSite($siteId)
    {
        $site = $this->get('tagcade.domain_manager.site')->find($siteId);

        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        $this->checkUserPermission($site);

        return $site;
    }

    /**
     * @return ReportBuilderInterface
     */
    protected function getReportBuilder()
    {
        return $this->get('tagcade.service.report.header_bidding.selector.report_builder');
    }

    /**
     * @return Params
     */
    protected function getParams()
    {
        $params = $this->get('fos_rest.request.param_fetcher')->all($strict = true);
        return $this->_createParams($params);
    }

    protected function getResult($result)
    {
        if ($result === false) {
            throw new NotFoundHttpException('No reports found for that query');
        }

        return $result;
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
}
