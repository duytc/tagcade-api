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
use Tagcade\Handler\Handlers\Core\ChannelHandlerAbstract;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;

/**
 * @Rest\RouteResource("Channel")
 */
class ChannelController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all channels
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return ChannelInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single channel for the given id
     *
     * @Rest\Get("/channels/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\ChannelInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get sites of channel for the given id
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\SiteInterface[]
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getSitesAction($id)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->one($id);

        return $channel->getSites();
    }

    /**
     * Get Channels include Sites that have not linked to library AdSlot
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     *
     * @Rest\Get("channels/nodeployments")
     *
     * @Rest\QueryParam(name="slotLibrary", requirements="\d+")
     *
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @return array
     */
    public function getChannelsIncludeSitesHaveNoAdSlotReferenceToAdSlotLibraryAction()
    {
        /** @var ParamFetcherInterface $paramFetcher */
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $slotLibraryId = $paramFetcher->get('slotLibrary');
        $slotLibraryId = filter_var($slotLibraryId, FILTER_VALIDATE_INT);

        $slotLibrary = $this->get('tagcade.domain_manager.library_ad_slot')->find($slotLibraryId);
        if (!$slotLibrary instanceof BaseLibraryAdSlotInterface) {
            throw new NotFoundHttpException('Not found that slot library');
        }

        $this->checkUserPermission($slotLibrary, 'edit');

        return $this->get('tagcade.domain_manager.channel')->getChannelsIncludeSitesUnreferencedToLibraryAdSlot($slotLibrary);
    }

    /**
     * get Channels has at least one Site
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     *
     * @Rest\Get("/channels/havesite")
     *
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @return array
     */
    public function getChannelsHaveSiteAction()
    {
        return $this->get('tagcade.domain_manager.channel')->getChannelsHaveSiteForUser($this->getUser());
    }

    /**
     * Get the javascript display ad tags for this channel
     *
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     *
     * @param int $id
     * @return array
     */
    public function getJstagsAction($id)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->one($id);

        return $this->get('tagcade.service.tag_generator')
            ->getTagsForChannel($channel);
    }

    /**
     * Create a channel from the submitted data
     *
     * @ApiDoc(
     *  section="Channels",
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
     * Update an existing channel from the submitted data or create a new channel at a specific location
     *
     * @ApiDoc(
     *  resource = true,
     *  section="Channels",
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
         * @var ChannelInterface $channel
         */
        $channel = $this->one($id);

        if (array_key_exists('publisher', $request->request->all())) {
            $publisher = (int)$request->get('publisher');
            if ($channel->getPublisherId() != $publisher) {
                throw new InvalidArgumentException('publisher in invalid');
            }
        }

        return $this->patch($request, $id);
    }

    /**
     * Delete an existing channel
     *
     * @ApiDoc(
     *  section="Channels",
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

    /**
     * Delete one site in sites list for a existing channel
     *
     * @ApiDoc(
     *  section="Channels",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @Rest\Delete("/channels/{channelId}/site/{siteId}", requirements={"channelId" = "\d+", "siteId" = "\d+"})
     *
     * @param int $channelId the channel id
     * @param int $siteId the site id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteSiteForChannelAction($channelId, $siteId)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getOr404($channelId);

        $this->checkUserPermission($channel, 'edit');

        $result = $this->get('tagcade.domain_manager.channel')->deleteSiteForChannel($channel, $siteId);

        return $this->view(null, ($result > 0 ? Codes::HTTP_NO_CONTENT : Codes::HTTP_NOT_FOUND));
    }

    protected function getResourceName()
    {
        return 'channel';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_channel';
    }

    /**
     * @return ChannelHandlerAbstract
     */
    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.channel');
    }
}
