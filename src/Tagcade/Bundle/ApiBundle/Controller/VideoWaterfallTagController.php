<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\VideoWaterfallTagRepository;


/**
 * @Rest\RouteResource("videowaterfalltag")
 */
class VideoWaterfallTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all VideoWaterfallTags
     *
     * @Rest\View(serializerGroups={"videoWaterfallTag.summary", "user.summary", "videoPublisher.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary"})
     *
     * @Rest\QueryParam(name="publisherId", nullable=true, requirements="\d+", description="the publisher id")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param $request
     * @return VideoWaterfallTagInterface[]
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->get('page') <= 0) {
            return $this->all();
        }

        /** @var VideoWaterfallTagRepository $waterfallTagRepository */
        $waterfallTagRepository = $this->get('tagcade.repository.video_waterfall_tag');
        $qb = $waterfallTagRepository->getWaterfallTagForUserWithPagination($this->getUser(), $this->getParams());
        return $this->getPagination($qb, $request);
    }

    /**
     * Get all VideoWaterfallTags which does not use a LibraryVideoDemandAdTag
     *
     * @Rest\Get("/videowaterfalltags/notlinktolibrary")
     *
     * @Rest\View(serializerGroups={"videoWaterfallTag.summary", "user.summary", "videoPublisher.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary"})
     *
     * @Rest\QueryParam(name="libraryVideoDemandAdTag", nullable=false, requirements="\d+", description="the LibraryVideoDemandAdTag id")
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return \Tagcade\Model\Core\VideoWaterfallTagInterface[]
     */
    public function getNotLinkToLibraryAction(Request $request)
    {
        $libraryVideoDemandAdTagId = $request->query->get('libraryVideoDemandAdTag', null);

        if (null == $libraryVideoDemandAdTagId) {
            throw new BadRequestHttpException(sprintf('required libraryVideoDemandAdTag'));
        }

        $libraryVideoDemandAdTag = $this->get('tagcade.domain_manager.library_video_demand_ad_tag')->find($libraryVideoDemandAdTagId);

        if (!$libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            throw new InvalidArgumentException(sprintf('library video demand ad tag %d does not exist', $libraryVideoDemandAdTagId));
        }

        return $this->get('tagcade.domain_manager.video_waterfall_tag')->getWaterfallTagsNotLinkToLibraryVideoDemandAdTag($libraryVideoDemandAdTag, $this->getUser());
    }

    /**
     * @Rest\Get("/videowaterfalltags/fordemandpartner")
     *
     * @Rest\View(serializerGroups={"videoWaterfallTag.summary", "user.summary", "videoPublisher.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary"})
     *
     * @Rest\QueryParam(name="videoDemandPartner", nullable=false, requirements="\d+", description="the videoDemandPartner id")
     * @param Request $request
     * @return mixed
     */
    public function getWaterfallTagsForVideoDemandPartnerAction(Request $request)
    {
        $videoDemandPartner = $request->query->get('videoDemandPartner', null);

        if (null == $videoDemandPartner) {
            throw new BadRequestHttpException(sprintf('required libraryVideoDemandAdTag'));
        }

        $videoDemandPartner = $this->get('tagcade.domain_manager.video_demand_partner')->find($videoDemandPartner);

        if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
            throw new InvalidArgumentException(sprintf('video demand partner %d does not exist', $videoDemandPartner));
        }

        return $this->get('tagcade.domain_manager.video_waterfall_tag')->getWaterfallTagsForVideoDemandPartner($videoDemandPartner);
    }

    /**
     * Get a single video ad tag for the given id
     *
     * @Rest\View(serializerGroups={"videoWaterfallTag.summary", "user.summary", "videoPublisher.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "videoDemandPartner.summary"})
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\VideoWaterfallTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get vast tag link for video waterfall tag
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param Request $request
     * @param int $id
     * @return View
     * @throws BadRequestHttpException when the query params is invalid
     */
    public function getVasttagAction(Request $request, $id)
    {
        /** @var VideoWaterfallTagInterface $videoWaterfallTag */
        $videoWaterfallTag = $this->one($id);

        // get "secure" param
        $isSecure = $request->query->get('secure', null);

        if (null == $isSecure || !in_array($isSecure, ['true', 'false'])) {
            throw new BadRequestHttpException('Missing required query param "secure" (true/false)');
        }

        $isSecure = filter_var($isSecure, FILTER_VALIDATE_BOOLEAN);

        return $this->get('tagcade.service.video_vast_tag_generator')->createVideoVastTags($videoWaterfallTag, $isSecure);
    }

    /**
     * Create a video ad tag from the submitted data
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
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
     * @Rest\Post("/videowaterfalltags/{id}/positions", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @return mixed
     */
    public function postWaterfallTagItemPositionAction(Request $request, $id)
    {
        /** @var VideoWaterfallTagInterface $videoWaterfallTag */
        $videoWaterfallTag = $this->one($id);

        $newVideoWaterfallTagItemOrderIds = $request->request->get('videoWaterfallTagItems', null);

        if (!is_array($newVideoWaterfallTagItemOrderIds)) {
            throw new BadRequestHttpException('Missing videoWaterfallTagItems (ids) is an array');
        }


        return $result = array_values(
            $this->get('tagcade_app.service.core.video_waterfall_tag_item.video_waterfall_tag_item_position_editor')
                ->setVideoWaterfallTagItemPositionForVideoWaterfallTag($videoWaterfallTag, $newVideoWaterfallTagItemOrderIds)
        );
    }

    /**
     * Update an existing video ad tag from the submitted data or create a new video ad tag
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
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
     * Update an existing video ad tag from the submitted data or create a new video ad tag at a specific location
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
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
         * @var VideoWaterfallTagInterface $videoWaterfallTag
         */
//        $videoWaterfallTag = $this->one($id);
//
//        if (array_key_exists('publisher', $request->request->all())) {
//            $publisher = (int)$request->get('publisher');
//            if ($videoWaterfallTag->getPublisher()->getId() != $publisher) {
//                throw new InvalidArgumentException('publisher in invalid');
//            }
//        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing video ad tag
     *
     * @ApiDoc(
     *  section = "Video Waterfall Tags",
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

    protected function getLogger()
    {
        return $this->get('logger');
    }

    protected function getResourceName()
    {
        return 'videowaterfalltag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_videowaterfalltag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.video_waterfall_tag');
    }
}
