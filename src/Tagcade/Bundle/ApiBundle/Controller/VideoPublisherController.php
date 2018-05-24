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
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\AdminInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\VideoWaterfallTag\VideoWaterfallTagParam;


/**
 * @Rest\RouteResource("videopublisher")
 */
class VideoPublisherController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all VideoPublishers
     *
     * @Rest\View(serializerGroups={"videoPublisher.summary", "user.summary", "videoWaterfallTag.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary"})
     *
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\QueryParam(name="publisherId", nullable=true, description="the publisher id which is used for filtering video publishers")
     * @ApiDoc(
     *  section = "Video Publishers",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return VideoPublisherInterface[]
     */
    public function cgetAction(Request $request)
    {
        $role = $this->getUser();

        $videoPublisherManager = $this->get('tagcade.domain_manager.video_publisher');
        $videoPublisherRepository = $this->get('tagcade.repository.video_publisher');

        $params = $this->getParams();

        if ($request->query->get('page') > 0) {
            $qb = $videoPublisherRepository->getVideoPublishersForPublisherWithPagination($this->getUser(), $params);
            return $this->getPagination($qb, $request);
        }

        if ($role instanceof PublisherInterface) {
            return $videoPublisherManager->getVideoPublishersForPublisher($role);
        }

        if ($role instanceof AdminInterface && $params->getPublisherId() > 0) {
            $publisherId = $params->getPublisherId();
            $publisherManager = $this->get('tagcade_user.domain_manager.publisher');
            $publisher = $publisherManager->findPublisher($publisherId);
            if (!$publisher instanceof PublisherInterface) {
                return [];
            }

            return $videoPublisherManager->getVideoPublishersForPublisher($publisher);
        }

        return $this->all();
    }

    /**
     * Get a single video publisher for the given id
     *
     * @Rest\View(serializerGroups={"videoPublisher.summary", "user.summary", "videoWaterfallTag.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "videoDemandPartner.summary"})
     *
     * @ApiDoc(
     *  section = "Video Publishers",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\VideoPublisherInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get vast tag links for video publisher
     *
     * @ApiDoc(
     *  section = "Video Publishers",
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
    public function getVasttagsAction(Request $request, $id)
    {
        /** @var VideoPublisherInterface $videoPublisher */
        $videoPublisher = $this->one($id);

        // get "secure" param
        $isSecure = $request->query->get(VideoWaterfallTagParam::PARAM_SECURE, null);

        if (null == $isSecure || !in_array($isSecure, ['true', 'false'])) {
            throw new BadRequestHttpException('Missing required query param "secure" (true/false)');
        }

        $isSecure = filter_var($isSecure, FILTER_VALIDATE_BOOLEAN);

        $macros = $request->query->get(VideoWaterfallTagParam::PARAM_MACROS, '');
        $macros = json_decode($macros);

        $videoWaterfallTagParam = new VideoWaterfallTagParam();
        $videoWaterfallTagParam->setSecure($isSecure);
        $videoWaterfallTagParam->setMacros($macros);

        return $this->get('tagcade.service.video_vast_tag_generator')->getVideoVastTagsForVideoPublisher($videoPublisher, $videoWaterfallTagParam);
    }

    /**
     * Get all videoWaterfallTags for video publisher
     *
     * @Rest\Get("/videopublishers/{id}/videowaterfalltags", requirements="")
     * @Rest\QueryParam(name="page", requirements="\d+", nullable=true, description="the page to get")
     * @Rest\QueryParam(name="limit", requirements="\d+", nullable=true, description="number of item per page")
     * @Rest\QueryParam(name="searchField", nullable=true, description="field to filter, must match field in Entity")
     * @Rest\QueryParam(name="searchKey", nullable=true, description="value of above filter")
     * @Rest\QueryParam(name="sortField", nullable=true, description="field to sort, must match field in Entity and sortable")
     * @Rest\QueryParam(name="orderBy", nullable=true, description="value of sort direction : asc or desc")
     * @Rest\View(serializerGroups={"videoWaterfallTag.summary", "user.summary", "videoPublisher.summary", "videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary"})
     *
     * @ApiDoc(
     *  section = "Video Publishers",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id
     * @param Request $request
     * @return View
     */
    public function getVideoWaterfallTagsAction($id, Request $request)
    {
        /** @var VideoPublisherInterface $videoPublisher */
        $videoPublisher = $this->one($id);

        $videoWaterfallTagManager = $this->get('tagcade.domain_manager.video_waterfall_tag');
        $videoWaterfallTagRepository = $this->get('tagcade.repository.video_waterfall_tag');

        if ($request->query->get('page') > 0) {
            $qb = $videoWaterfallTagRepository->getVideoWaterfallTagsForVideoPublisherWithPagination($videoPublisher, $this->getParams());
            return $this->getPagination($qb, $request);
        }

        return ($videoPublisher instanceof PublisherInterface)
            ? $videoWaterfallTagManager->getVideoWaterfallTagsForVideoPublisher($videoPublisher)
            : $this->all();
    }

    /**
     * Create a video publisher from the submitted data
     *
     * @ApiDoc(
     *  section = "Video Publishers",
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
     * Update an existing video publisher from the submitted data or create a new video publisher
     *
     * @ApiDoc(
     *  section = "Video Publishers",
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
     * Update an existing video publisher from the submitted data or create a new video publisher at a specific location
     *
     * @ApiDoc(
     *  section = "Video Publishers",
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
     * Delete an existing video publisher
     *
     * @ApiDoc(
     *  section = "Video Publishers",
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
        return 'videopublisher';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_videopublisher';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.video_publisher');
    }
}
