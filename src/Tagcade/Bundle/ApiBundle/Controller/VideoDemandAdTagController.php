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
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\AdminInterface;


/**
 * @Rest\RouteResource("videodemandadtag")
 */
class VideoDemandAdTagController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all videoDemandAdTags
     *
     * @Rest\View(serializerGroups={"videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "user.summary", "videoDemandPartner.summary"})
     *
     * @Rest\QueryParam(name="publisher", nullable=true, requirements="\d+", description="the publisher id")
     *
     * @ApiDoc(
     *  section = "Video AdSources",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return VideoDemandAdTagInterface[]
     */
    public function cgetAction()
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $publisherId = $paramFetcher->get('publisher');

        if (!$this->getUser() instanceof AdminInterface || ($this->getUser() instanceof AdminInterface && $publisherId == null)) {
            $all = $this->all();
        } else {
            $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

            $all = $this->get('tagcade.domain_manager.video_demand_ad_tag')->getVideoDemandAdTagsForPublisher($publisher);
        }

        $this->checkUserPermission($all);

        return $all;
    }

    /**
     * Get all video ad source not belong to not be long To video tag item
     *
     * @Rest\View(serializerGroups={"videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "user.summary", "videoDemandPartner.summary"})
     *
     * @Rest\Get("/videodemandadtags/notBelongToVideoTagItem")
     *
     * @ApiDoc(
     *  section="demandAdTags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return VideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTagsNotBelongToVideoTagItemAction()
    {
        $all = $this->get('tagcade.domain_manager.video_demand_ad_tag')->getVideoDemandAdTagsNotBelongToVideoTagItem($this->getUser());

        $this->checkUserPermission($all);

        return $all;
    }

    /**
     * Get a single video ad source for the given id
     *
     * @Rest\View(serializerGroups={"videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "user.summary", "videoDemandPartner.summary", "videoWaterfallTagItem.detail", "videoWaterfallTag.summary"})
     *
     * @ApiDoc(
     *  section = "Video AdSources",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\VideoDemandAdTagInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a video ad source from the submitted data
     *
     * @Rest\View(serializerGroups={"videoDemandAdTag.summary", "libraryVideoDemandAdTag.summary", "user.summary", "videoDemandPartner.summary"})
     *
     * @ApiDoc(
     *  section = "Video AdSources",
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
     * Update an existing video ad source from the submitted data or create a new video ad source
     *
     * @ApiDoc(
     *  section = "Video AdSources",
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


//    /**
//     * @Rest\QueryParam(name="active", requirements="(true|false)", nullable=true)
//     * @Rest\QueryParam(name="waterfallTagId", requirements="(true|false)", nullable=true)
//     * @param $id
//     * @return View
//     */
//    public function putStatusAction($id)
//    {
//        /** @var VideoDemandPartnerInterface $demandPartner */
//        $demandPartner = $this->getOr404($id);
//
//        $this->checkUserPermission($demandPartner, 'edit');
//        /** @var ParamFetcherInterface $paramFetcher */
//        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
//        $active = $paramFetcher->get('active');
//        $waterfallTagId = $paramFetcher->get('waterfallTagId');
//        $active = filter_var($active, FILTER_VALIDATE_BOOLEAN);
//
//        $this->get('tagcade.worker.manager')->updateVideoDemandAdTagStatusForDemandPartner($id, $active, $waterfallTagId);
//
//        return $this->view(null, Codes::HTTP_NO_CONTENT);
//    }

    /**
     * Update an existing video ad source from the submitted data or create a new video ad source at a specific location
     *
     * @ApiDoc(
     *  section = "Video AdSources",
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
         * @var VideoDemandAdTagInterface $videoDemandAdTag
         */
        $videoDemandAdTag = $this->one($id);

        if (array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if ($videoDemandAdTag->getPublisher()->getId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing video ad source
     *
     * @ApiDoc(
     *  section = "Video AdSources",
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
        return 'videodemandadtag';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_videodemandadtag';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.video_demand_ad_tag');
    }
}