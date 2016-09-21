<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\User\Role\AdminInterface;


/**
 * @Rest\RouteResource("videowaterfalltagitem")
 */
class VideoWaterfallTagItemController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all videoWaterfallTagItems
     *
     * @Rest\View(serializerGroups={"videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "videoDemandPartner.summary", "user.summary"})
     *
     * @Rest\QueryParam(name="publisher", nullable=true, requirements="\d+", description="the publisher id")
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return VideoWaterfallTagItemInterface[]
     */
    public function cgetAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $publisherId = $paramFetcher->get('publisher');

        if (!$this->getUser() instanceof AdminInterface || ($this->getUser() instanceof AdminInterface && $publisherId == null)) {
            $all = $this->all();
        } else {
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            $all = $this->get('tagcade.domain_manager.video_waterfall_tag_item')->getVideoWaterfallTagItemsForPublisher($publisher);
        }

        $this->checkUserPermission($all);

        return $all;
    }

    /**
     * Get a single video ad tag item for the given id
     *
     * @Rest\View(serializerGroups={"videoWaterfallTagItem.summary", "user.summary"})
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\VideoWaterfallTagItemInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get a all video ad tag item for one ad tag
     *
     * @Rest\View(serializerGroups={"videoWaterfallTagItem.summary", "videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "videoDemandPartner.summary", "user.summary"})
     *
     * @Rest\Get("videowaterfalltagitems/adtag/{id}", requirements={"id" = "\d+"})
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @throws \Exception
     * @return \Tagcade\Model\Core\VideoWaterfallTagItemInterface
     */
    public function getAdTagItemsByAdTagAction($id)
    {
        $adTag = $this->get('tagcade.domain_manager.video_waterfall_tag')->find($id);

        if (!$adTag instanceof VideoWaterfallTag) {
            throw new \Exception(sprintf('Can not find video ad tag with id =%d',$id));
        }

        return $this->get('tagcade.domain_manager.video_waterfall_tag_item')->getVideoWaterfallTagItemsForAdTag($adTag);
    }

    /**
     * Create a video ad tag item from the submitted data
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
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
     * Update an existing video ad tag item from the submitted data or create a new video ad tag item
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
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
     * Update an existing video ad tag item from the submitted data or create a new video ad tag item at a specific location
     *
     * @ApiDoc(
     *  section = "Video AdTagItems",
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
         * @var VideoWaterfallTagItemInterface $videoWaterfallTagItem
         */
        $videoWaterfallTagItem = $this->one($id);

        if (array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if ($videoWaterfallTagItem->getVideoWaterfallTag()->getPublisher()->getId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing video ad tag item
     *
     * @ApiDoc(
     *  section = "Video AdTagITems",
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
        return 'videowaterfalltagitem';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_videowaterfalltagitem';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.video_waterfall_tag_item');
    }
}
