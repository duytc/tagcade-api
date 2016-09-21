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
     * @Rest\QueryParam(name="publisher", nullable=true, requirements="\d+", description="the publisher id")
     *
     * @ApiDoc(
     *  section = "Video Publishers",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return VideoPublisherInterface[]
     */
    public function cgetAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $publisherId = $paramFetcher->get('publisher');

        if (!$this->getUser() instanceof AdminInterface || ($this->getUser() instanceof AdminInterface && $publisherId == null)) {
            $all = $this->all();
        } else {
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            $all = $this->get('tagcade.domain_manager.video_publisher')->getVideoPublishersForPublisher($publisher);
        }

        $this->checkUserPermission($all);

        return $all;
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
        $isSecure = $request->query->get('secure', null);

        if (null == $isSecure || !in_array($isSecure, ['true', 'false'])) {
            throw new BadRequestHttpException('Missing required query param "secure" (true/false)');
        }

        $isSecure = filter_var($isSecure, FILTER_VALIDATE_BOOLEAN);

        return $this->get('tagcade.service.video_vast_tag_generator')->getVideoVastTagsForVideoPublisher($videoPublisher, $isSecure);
    }

    /**
     * Get all videoWaterfallTags for video publisher
     *
     * @Rest\Get("/videopublishers/{id}/videowaterfalltags", requirements="")
     *
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
     * @return View
     * @throws BadRequestHttpException when the query params is invalid
     */
    public function getVideoWaterfallTagsAction($id)
    {
        /** @var VideoPublisherInterface $videoPublisher */
        $videoPublisher = $this->one($id);

        return $this->get('tagcade.domain_manager.video_waterfall_tag')->getVideoWaterfallTagsForVideoPublisher($videoPublisher);
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
