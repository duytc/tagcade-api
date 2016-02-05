<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;

/**
 * @Security("has_role('ROLE_ADMIN') or ( has_role('ROLE_PUBLISHER') and has_role('MODULE_DISPLAY') )")
 *
 * Only allow admins and publishers with the display module enabled
 */
class PerformanceReportController extends FOSRestController
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

        return $this->getResult(
            $this->getReportBuilder()->getPublisherReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/accounts/{publisherId}/adnetworks", requirements={"publisherId" = "\d+"})
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
    public function getPublisherAdNetworksAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

        return $this->getResult(
            $this->getReportBuilder()->getPublisherAdNetworksReport($publisher, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}", requirements={"adNetworkId" = "\d+"})
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
     * @param int $adNetworkId
     * @return array
     */
    public function getAdnetworkAction($adNetworkId)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);

        return $this->getResult(
            $this->getReportBuilder()->getAdNetworkReport($adNetwork, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/sites", requirements={"adNetworkId" = "\d+"})
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
     * @param int $adNetworkId
     * @return array
     */
    public function getAdnetworkSitesAction($adNetworkId)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);

        return $this->getResult(
            $this->getReportBuilder()->getAdNetworkSitesReport($adNetwork, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/sites/{siteId}", requirements={"adNetworkId" = "\d+", "siteId" = "\d+"})
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
     * @param int $adNetworkId
     * @param int $siteId ID
     *
     * @return array
     */
    public function getAdNetworkSiteAction($adNetworkId, $siteId)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);
        $site = $this->getSite($siteId);

        return $this->getResult(
            $this->getReportBuilder()->getAdNetworkSiteReport($adNetwork, $site, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/adtags", requirements={"adNetworkId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="start date to retrieve report")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="end date to retrieve report")
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true, description="grouped report to see summary or just date range report")
     *
     * @ApiDoc(
     *  section = "Performance Report",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $adNetworkId
     *
     *
     * @return array
     */
    public function getAdNetworkAdTagsAction($adNetworkId)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);

        return $this->getResult(
            $this->getReportBuilder()->getAdNetworkAdTagsReport($adNetwork, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adnetworks/{adNetworkId}/sites/{siteId}/adtags", requirements={"adNetworkId" = "\d+", "siteId" = "\d+"})
     *
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     *
     * @param int $adNetworkId
     * @param int $siteId ID
     *
     * @return array
     */
    public function getAdNetworkSiteAdTagsAction($adNetworkId, $siteId)
    {
        $adNetwork = $this->getAdNetwork($adNetworkId);
        $site = $this->getSite($siteId);

        return $this->getResult(
            $this->getReportBuilder()->getAdNetworkSiteAdTagsReport($adNetwork, $site, $this->getParams())
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
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param int $publisherId
     * @return array
     */
    public function getPublisherSitesAction($publisherId)
    {
        $publisher = $this->getPublisher($publisherId);

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

        return $this->getResult(
            $this->getReportBuilder()->getSiteReport($site, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/sites/{siteId}/adnetworks", requirements={"siteId" = "\d+"})
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
     * @return array
     */
    public function getSiteAdNetworksAction($siteId)
    {
        $site = $this->getSite($siteId);

        return $this->getResult(
            $this->getReportBuilder()->getSiteAdNetworksReport($site, $this->getParams())
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

        return $this->getResult(
            $this->getReportBuilder()->getSiteAdSlotsReport($site, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/sites/{siteId}/adtags", requirements={"siteId" = "\d+"})
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
    public function getSiteAdTagsAction($siteId)
    {
        $site = $this->getSite($siteId);

        return $this->getResult(
            $this->getReportBuilder()->getSiteAdTagsReport($site, $this->getParams())
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
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad slot does not exist');
        }

        $this->checkUserPermission($adSlot);

        return $this->getResult(
            $this->getReportBuilder()->getAdSlotReport($adSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adslots")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true)
     * @Rest\QueryParam(name="group", requirements="(true|false)", nullable=true)
     */
    public function getAllAdSlotsAction()
    {
        $user = $this->getUser();
        if ($user instanceof PublisherInterface) {
            return $this->getResult(
                $this->getReportBuilder()->getPublisherAdSlotsReport($user, $this->getParams())
            );
        }

        return $this->getResult(
            $this->getReportBuilder()->getAllAdSlotsReport($this->getParams())
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
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot) {
            throw new NotFoundHttpException('That ron ad slot does not exist');
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adslots/{adSlotId}/adtags", requirements={"adSlotId" = "\d+"})
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
    public function getAdSlotAdTagsAction($adSlotId)
    {
        $adSlot = $this->get('tagcade.domain_manager.ad_slot')->find($adSlotId);

        if (!$adSlot) {
            throw new NotFoundHttpException('That ad tag does not exist');
        }

        $this->checkUserPermission($adSlot);

        return $this->getResult(
            $this->getReportBuilder()->getAdSlotAdTagsReport($adSlot, $this->getParams())
        );
    }

    /**
     * There's only on ad slot in a single site that was created from an ron ad slot.
     * Hence report for ron ad slot break down by site is corresponding to break down by ad slot
     *
     *
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
    public function getRonAdSlotSegmentsAction($ronAdSlotId)
    {
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot) {
            throw new NotFoundHttpException('That ron ad slot does not exist');
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotSegmentsReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * There's only on ad slot in a single site that was created from an ron ad slot.
     * Hence report for ron ad slot break down by site is corresponding to break down by ad slot
     *
     *
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
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot) {
            throw new NotFoundHttpException('That ron ad slot does not exist');
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotSitesReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/ronadslots/{ronAdSlotId}/adtags", requirements={"ronAdSlotId" = "\d+"})
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
    public function getRonAdSlotAdTagsAction($ronAdSlotId)
    {
        /** @var RonAdSlotInterface $ronAdSlot */
        $ronAdSlot = $this->get('tagcade.domain_manager.ron_ad_slot')->find($ronAdSlotId);

        if (!$ronAdSlot) {
            throw new NotFoundHttpException('That ron ad slot does not exist');
        }

        $this->checkUserPermission($ronAdSlot);

        return $this->getResult(
            $this->getReportBuilder()->getRonAdSlotAdTagsReport($ronAdSlot, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/segments/{segmentId}", requirements={"segmentId" = "\d+"})
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
     * @param $segmentId
     * @return array
     */
    public function getSegmentAction($segmentId)
    {
        $segment = $this->get('tagcade.domain_manager.segment')->find($segmentId);

        if (!$segment) {
            throw new NotFoundHttpException('That segment does not exist');
        }

        $this->checkUserPermission($segment);

        return $this->getResult(
            $this->getReportBuilder()->getSegmentReport($segment, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/segments/{segmentId}/ronadslots", requirements={"segmentId" = "\d+"})
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
     * @param $segmentId
     * @return array
     */
    public function getSegmentRonAdSlotsAction($segmentId)
    {
        $segment = $this->get('tagcade.domain_manager.segment')->find($segmentId);

        if (!$segment) {
            throw new NotFoundHttpException('That segment does not exist');
        }

        $this->checkUserPermission($segment);

        return $this->getResult(
            $this->getReportBuilder()->getSegmentRonAdSlotsReport($segment, $this->getParams())
        );
    }

    /**
     * @Rest\Get("/adtags/{adTagId}", requirements={"adTagId" = "\d+"})
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
     * @param int $adTagId
     *
     * @return array
     */
    public function getAdTagAction($adTagId)
    {
        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($adTagId);

        if (!$adTag) {
            throw new NotFoundHttpException('That ad tag does not exist');
        }

        $this->checkUserPermission($adTag);

        return $this->getResult(
            $this->getReportBuilder()->getAdTagReport($adTag, $this->getParams())
        );
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
     * @param int $adNetworkId
     * @return \Tagcade\Model\Core\AdNetworkInterface
     */
    protected function getAdNetwork($adNetworkId)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That ad network does not exist');
        }

        $this->checkUserPermission($adNetwork);

        return $adNetwork;
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
        return $this->get('tagcade.service.report.performance_report.display.selector.report_builder');
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
