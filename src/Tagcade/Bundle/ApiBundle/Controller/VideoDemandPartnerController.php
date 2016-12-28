<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\VideoDemandPartnerRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform\WaterfallTagInterface;
use Tagcade\Service\StringUtilTrait;


/**
 * @Rest\RouteResource("videodemandpartner")
 */
class VideoDemandPartnerController extends RestControllerAbstract implements ClassResourceInterface
{
    use StringUtilTrait;

    /**
     * Get all demand partner
     *
     * @Rest\View(serializerGroups={"videoDemandPartner.summary", "user.summary"})
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return VideoDemandPartnerInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();

        $videoDemandPartnerManager = $this->get('tagcade.manager.video_demand_partner');
        $videoDemandPartnerRepository = $this->get('tagcade.repository.video_demand_partner');

        if ($request->query->get('page') > 0) {
            $qb = $videoDemandPartnerRepository->getVideoDemandPartnersForPublisherWithPagination($this->getUser(), $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $videoDemandPartnerManager->getVideoDemandPartnersForPublisher($role)
            : $this->all();
    }

    /**
     * Get a single video demand partner for the given id
     *
     * @Rest\View(serializerGroups={"videoDemandPartner.summary", "user.summary"})
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\VideoDemandPartnerInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get all sites belonging to this ad network
     *
     * @Rest\View(serializerGroups={"waterfalltagstatus.detail", "videoWaterfallTag.summary", "videoPublisher.summary"})
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true)
     * @Rest\QueryParam(name="size", requirements="\d+", nullable=true)
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return WaterfallTagInterface[]
     *
     */
    public function getWaterfalltagsAction($id, Request $request)
    {
        /** @var VideoDemandPartnerInterface $demandPartner */
        $demandPartner = $this->one($id);

        $page = $request->query->get('page', null);

        if ($page === null) {
            return $this->get('tagcade_app.service.core.video_demand_partner.demand_partner_service')->getVideoWaterfallTagForVideoDemandPartner($demandPartner);
        }

        $size = $request->query->get('size', 10);
        $offset = ($page - 1) * $size;
        $waterfallTagStatus = $this->get('tagcade_app.service.core.video_demand_partner.demand_partner_service')->getVideoWaterfallTagForVideoDemandPartner($demandPartner);

        return array (
            'totalRecord' => count($waterfallTagStatus),
            'records' => array_slice($waterfallTagStatus, $offset, $size),
            'itemPerPage' => $size,
            'currentPage' => $page
        );
    }

    /**
     *
     * Get all active demand ad tags belonging to this demand partner
     *
     * @Rest\Get("/videodemandpartners/{id}/videodemandadtags", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"videoDemandAdTag.detail", "libraryVideoDemandAdTag.summary", "videoDemandPartner.summary", "videoPublisher.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\Core\VideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTagsAction($id)
    {
        /** @var VideoDemandPartnerInterface $videoDemandPartner */
        $videoDemandPartner = $this->one($id);

        return $this->get('tagcade.domain_manager.video_demand_ad_tag')
            ->getVideoDemandAdTagsForDemandPartner($videoDemandPartner);
    }

    /**
     *
     * Get all active demand ad tags belonging to this demand partner
     *
     * @Rest\Get("/videodemandpartners/{id}/libraryvideodemandadtags", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"libraryVideoDemandAdTag.summaryWithLinkedCount", "videoDemandPartner.summary", "videoPublisher.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @return \Tagcade\Model\Core\VideoDemandAdTagInterface[]
     */
    public function getLibraryVideoDemandAdTagsAction($id)
    {
        /** @var VideoDemandPartnerInterface $videoDemandPartner */
        $videoDemandPartner = $this->one($id);

        return $this->get('tagcade.domain_manager.library_video_demand_ad_tag')
            ->getLibraryVideoDemandAdTagsForDemandPartner($videoDemandPartner);
    }

    /**
     * Create a video demand partner from the submitted data
     *
     * @Rest\QueryParam(name="publisher", nullable=true, requirements="\d+", description="the publisher id")
     * @Rest\QueryParam(name="name", nullable=true, description="name of video demand partner")
     *
     * @ApiDoc(
     *  section = "Video Demand Partners",
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
        return $this->postAndReturnEntityData($request);
    }

    /**
     * Update an existing video demand partner from the submitted data or create a new video demand partner
     *
     * @ApiDoc(
     *  section = "Video Demand partners",
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
     * @Rest\QueryParam(name="active", requirements="(true|false)", nullable=true)
     * @Rest\QueryParam(name="waterfallTagId", requirements="(true|false)", nullable=true)
     * @param $id
     * @param Request $request
     * @return View
     */
    public function putStatusAction(Request $request, $id)
    {
        /** @var VideoDemandPartnerInterface $demandPartner */
        $demandPartner = $this->getOr404($id);

        $this->checkUserPermission($demandPartner, 'edit');
        $allParams = $request->request->all();
        $waterfallTagId = null;
        if (array_key_exists('waterfallTagId', $allParams)) {
            $waterfallTagId = $allParams['waterfallTagId'];
        }
        $active = array_key_exists('active', $allParams) ? filter_var($allParams['active'], FILTER_VALIDATE_BOOLEAN) : false;

        $this->get('tagcade.worker.manager')->updateVideoDemandAdTagStatusForDemandPartner($id, $active, $waterfallTagId);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }
    /**
     * Update an existing video demand partner from the submitted data or create a new video demand partner at a specific location
     *
     * @ApiDoc(
     *  section = "Video Demand partners",
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
         * @var VideoDemandPartnerInterface $videoDemandPartner
         */
        $videoDemandPartner = $this->one($id);

        if (array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if ($videoDemandPartner->getPublisher()->getId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

//    /**
//     * Delete an existing video demand partner
//     *
//     * @ApiDoc(
//     *  section = "Video Demand partners",
//     *  resource = true,
//     *  statusCodes = {
//     *      204 = "Returned when successful",
//     *      400 = "Returned when the submitted data has errors"
//     *  }
//     * )
//     *
//     * @param int $id the resource id
//     *
//     * @return View
//     *
//     * @throws NotFoundHttpException when the resource not exist
//     */
//    public function deleteAction($id)
//    {
//        return $this->delete($id);
//    }

    protected function getLogger()
    {
        return $this->get('logger');
    }

    protected function getResourceName()
    {
        return 'videodemandpartner';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_videodemandpartner';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.video_demand_partner');
    }
}
