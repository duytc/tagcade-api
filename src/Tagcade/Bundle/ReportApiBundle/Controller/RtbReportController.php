<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\RtbReport\Selector\Params;
use Tagcade\Service\Report\RtbReport\Selector\ReportBuilderInterface;

/**
 * @Security("has_role('ROLE_ADMIN') or ( has_role('ROLE_PUBLISHER') and has_role('MODULE_DISPLAY') )")
 * Only allow admins and publishers with the rtb module report enabled
 */
class RtbReportController extends FOSRestController
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
     * @Security("has_role('ROLE_ADMIN')")
     * @Rest\Get("/platform/sites")
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
    public function getPlatformSitesAction()
    {
        return $this->getResult(
            $this->getReportBuilder()->getAllSitesReport($this->getParams())
        );
    }


    /**
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
        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPublisherReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/sites", requirements={"publisherId" = "\d+"})
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
     * @return array
     */
    public function getPublisherSitesAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);
        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPublisherSitesReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/sites/{siteId}", requirements={"siteId" = "\d+"})
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

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getSiteReport($site, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/sites/{siteId}/adslots", requirements={"siteId" = "\d+"})
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
     * @param int $siteId
     *
     * @return array
     */
    public function getSiteAdSlotsAction($siteId)
    {
        $site = $this->getSite($siteId);

        $publisher = $site->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getSiteAdSlotsReport($site, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adslots/{adSlotId}", requirements={"adSlotId" = "\d+"})
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
     * @param int $adSlotId
     *
     * @return array
     */
    public function getAdSlotAction($adSlotId)
    {
        /** @var ReportableAdSlotInterface $adSlot */
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $publisher = $adSlot->getSite()->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        $this->checkUserPermission($adSlot);

        return $this->getResult(
            $this->getReportBuilder()->getAdSlotReport($adSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/ronadslots/{ronAdSlotId}", requirements={"ronAdSlotId" = "\d+"})
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
     * @param int $ronAdSlotId
     *
     * @return array
     */
    public function getRonAdSlotAction($ronAdSlotId)
    {
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $publisher = $ronAdSlot->getLibraryAdSlot()->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/ronadslots/{ronAdSlotId}/sites", requirements={"ronAdSlotId" = "\d+"})
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
     * @param int $ronAdSlotId
     *
     * @return array
     */
    public function getRonAdSlotSitesAction($ronAdSlotId)
    {
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $publisher = $ronAdSlot->getLibraryAdSlot()->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotSiteReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/ronadslots/{ronAdSlotId}/segments", requirements={"ronAdSlotId" = "\d+"})
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
     * @param int $ronAdSlotId
     *
     * @return array
     */
    public function getRonAdSlotSegmentAction($ronAdSlotId)
    {
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot instanceof RonAdSlotInterface) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $this->checkUserPermission($ronAdSlot);

        $publisher = $ronAdSlot->getLibraryAdSlot()->getPublisher();

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotSegmentReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/ronadslots", requirements={"publisherId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @ApiDoc(
     *  section = "RTB Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherRonAdSlotAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new NotFoundHttpException();
        }

        if (!$publisher->hasRtbModule()) {
            throw new NotFoundHttpException();
        }

        return $this->getResult(
            $this->getReportBuilder()->getPublisherRonAdSlotReport($publisher, $this->getParams())
        );
    }


    /**
     * @return ReportBuilderInterface
     */
    protected function getReportBuilder()
    {
        return $this->get('tagcade.server.report.rtb_report.selector.report_builder');
    }


    /**
     * @param mixed $entity The entity instance
     * @return bool
     * @throws AccessDeniedException
     */
    protected function checkUserPermission($entity)
    {
        $securityContext = $this->get('security.context');

        // allow admins to everything
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // check voters
        if (false === $securityContext->isGranted('view', $entity)) {
            throw new AccessDeniedException(sprintf('You do not have permission to view this'));
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

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if (!$publisher instanceof PublisherInterface) {
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
}
