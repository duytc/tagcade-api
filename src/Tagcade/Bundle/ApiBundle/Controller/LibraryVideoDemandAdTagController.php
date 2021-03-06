<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\ExpressionInterface;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\VideoDemandAdTag\DeployLibraryVideoDemandAdTagServiceInterface;


/**
 * @Rest\RouteResource("LibraryVideoDemandAdTag")
 */
class LibraryVideoDemandAdTagController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;

    /**
     * Get all library video demand ad tags
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\View(
     *     serializerGroups={"libraryVideoDemandAdTag.detail", "videoDemandPartner.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return LibraryVideoDemandAdTagInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();

        $libraryVideoDemandAdTagManager = $this->get('tagcade.domain_manager.library_video_demand_ad_tag');
        $libraryVideoDemandAdTagRepository = $this->get('tagcade.repository.library_video_demand_ad_tag');


        if ($request->query->get('page') > 0) {
            $qb = $libraryVideoDemandAdTagRepository->getLibraryVideoDemandAdTagsForPublisherWithPagination($this->getUser(), $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return ($role instanceof PublisherInterface)
            ? $libraryVideoDemandAdTagManager->getLibraryVideoDemandAdTagsForPublisher($role)
            : $this->all();

    }

    /**
     * Get single library video demand ad tag
     *
     * @Rest\View(
     *     serializerGroups={"libraryVideoDemandAdTag.summary", "WaterfallPlacementRule.summary", "videoDemandPartner.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return LibraryVideoDemandAdTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     *
     * Get all video demand ad tags linked to this library video demand partner
     *
     * @Rest\Get("/libraryvideodemandadtags/{id}/videodemandadtags", requirements={"id" = "\d+"})
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\View(
     *      serializerGroups={"videoDemandAdTag.detail", "libraryVideoDemandAdTag.summary", "videoDemandPartner.summary", "videoPublisher.summary", "videoWaterfallTagItem.detail", "videoWaterfallTag.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @param Request $request
     * @return \Tagcade\Model\Core\VideoDemandAdTagInterface[]
     */
    public function getLinkedVideoDemandAdTagsAction($id, Request $request)
    {
        $libraryVideoDemandAdTag = $this->one($id);

        $libraryVideoDemandAdTagManager = $this->get('tagcade.domain_manager.video_demand_ad_tag');
        $libraryVideoDemandAdTagRepository = $this->get('tagcade.repository.video_demand_ad_tag');


        if ($request->query->get('page') > 0) {
            $qb = $libraryVideoDemandAdTagRepository->getVideoDemandAdTagsForLibraryVideoDemandAdTagWithPagination($libraryVideoDemandAdTag, $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return $libraryVideoDemandAdTagManager->getVideoDemandAdTagsForLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
    }

    /**
     *
     * Get all video demand ad tags linked to this library video demand partner
     *
     * @Rest\Get("/libraryvideodemandadtags/{id}/validwaterfalltags", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"videoWaterfallTag.summary", "user.summary"}
     * )
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
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
    public function getValidWaterfallTagsAction($id)
    {
        /** @var LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag */
        $libraryVideoDemandAdTag = $this->one($id);

        return $this->get('tagcade_app.service.core.video_waterfall_tag.video_waterfall_tag_service')
            ->getValidVideoWaterfallTagsForLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
    }

    /**
     * Create a library video demand ad tag from the submitted data
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
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
     * Create linked video demand ad tags to multi video waterfall tags for a library video demand ad tag from the submitted data
     *
     * @param Request $request
     * @param $id
     * @return View
     */
    public function postCreatelinksAction(Request $request, $id)
    {
        /** @var LibraryVideoDemandAdTagInterface $libraryDemandAdTag */
        $libraryDemandAdTag = $this->one($id);

        // fetch all params
        $waterfallIds = $request->request->get('waterfalls', null);
        $targeting = $request->request->get(ExpressionInterface::TARGETING, false);
        $targetingOverride = $request->request->get('targetingOverride', false);
        $priority = $request->request->get('priority', null);
        $rotationWeight = $request->request->get('rotationWeight', null);
        $active = $request->request->get('active', null);
        $position = $request->request->get('position', null);
        $shiftDown = $request->request->get('shiftDown', false);

        // validate: requested waterfalls as array
        if (!is_array($waterfallIds)) {
            throw new InvalidArgumentException('expect "waterfalls" to be an array');
        }

        // optional
        $targetingOverride = filter_var($targetingOverride, FILTER_VALIDATE_BOOLEAN);
        $priority = false == filter_var($priority, FILTER_VALIDATE_INT) ? null : filter_var($priority, FILTER_VALIDATE_INT);
        $rotationWeight = false == filter_var($rotationWeight, FILTER_VALIDATE_INT) ? null : filter_var($rotationWeight, FILTER_VALIDATE_INT);
        $active = filter_var($active, FILTER_VALIDATE_INT);
        $position = false == filter_var($position, FILTER_VALIDATE_INT) ? null : filter_var($position, FILTER_VALIDATE_INT);
        $shiftDown = filter_var($shiftDown, FILTER_VALIDATE_BOOLEAN);

        // do linking library demand ad tag to many video waterfall tags
        /** @var DeployLibraryVideoDemandAdTagServiceInterface $deployService */
        $deployService = $this->get('tagcade_app.service.core.video_demand_ad_tag.deploy_library_video_demand_ad_tag');
        $deployService->deployLibraryVideoDemandAdTagToWaterfalls($libraryDemandAdTag, $rule = null, $waterfallIds, $targeting, $targetingOverride, $priority, $rotationWeight, $active, $position, $shiftDown);

        return $this->view( null, Codes::HTTP_CREATED );
    }

    /**
     * Update an existing library video demand adTag from the submitted data or create a new library video demand ad tag
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
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
     * Update an existing library video demand ad tag from the submitted data or create a new library video demand ad tag at a specific location
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
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
     * Delete an existing library video demand ad tag
     *
     * @ApiDoc(
     *  section="Library video demand ad tags",
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

    protected function getResourceName()
    {
        return 'libraryvideodemandadtag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_libraryvideodemandadtag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.library_video_demand_ad_tag');
    }
}