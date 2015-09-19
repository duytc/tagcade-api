<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * @Rest\RouteResource("Site")
 */
class SiteController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all sites
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return SiteInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single site for the given id
     *
     * @Rest\Get("/sites/{id}", requirements={"id" = "\d+"})
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary"}
     * )
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param int $id the resource id
     *
     * @return \Tagcade\Model\Core\SiteInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Create a site from the submitted data
     *
     * @ApiDoc(
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
     * Update an existing site from the submitted data or create a new site
     *
     * @ApiDoc(
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
     * Update an existing site from the submitted data or create a new site at a specific location
     *
     * @ApiDoc(
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
     * Delete an existing site
     *
     * @ApiDoc(
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
     * Delete one channel in channels list for a existing site
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful",
     *      400 = "Returned when the submitted data has errors"
     *  }
     * )
     *
     * @Rest\Delete("/sites/{siteId}/channel/{channelId}", requirements={"siteId" = "\d+", "channelId" = "\d+"})
     *
     * @param int $siteId the site id
     * @param int $channelId the channel id
     *
     * @return View
     *
     * @throws NotFoundHttpException when the resource not exist
     */
    public function deleteChannelForSiteAction($siteId, $channelId)
    {
        /** @var SiteInterface $site */
        $site = $this->getOr404($siteId);

        $this->checkUserPermission($site, 'edit');

        $result = $this->get('tagcade.domain_manager.site')->deleteChannelForSite($site, $channelId);

        return $this->view(null, ($result > 0 ? Codes::HTTP_NO_CONTENT : Codes::HTTP_NOT_FOUND));
    }

    /**
     * Retrieve a list of ad slots for this site
     *
     * @param int $id
     * @return \Tagcade\Model\Core\BaseAdSlotInterface[]
     */
    public function getAdslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_slot')
            ->getAdSlotsForSite($site);
    }

    public function getDisplayadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.display_ad_slot')
            ->getAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of dynamic ad slots for this site
     *
     * @param int $id
     * @return \Tagcade\Model\Core\DynamicAdSlotInterface[]
     */
    public function getDynamicadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.dynamic_ad_slot')
            ->getDynamicAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of native ad slots for this site
     *
     * @param int $id
     * @return \Tagcade\Model\Core\NativeAdSlotInterface[]
     */
    public function getNativeadslotsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.native_ad_slot')
            ->getNativeAdSlotsForSite($site);
    }

    /**
     * Retrieve a list of active ad tags for this site
     *
     * @param int $id
     * @return \Tagcade\Model\Core\AdTagInterface[]
     */
    public function getAdtagsActiveAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.domain_manager.ad_tag')
            ->getAdTagsForSite($site, true);
    }

    /**
     * Get the javascript display ad tags for this site
     *
     * @param int $id
     * @return array
     */
    public function getJstagsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $this->get('tagcade.service.tag_generator')
            ->getTagsForSite($site);
    }

    /**
     * Retrieve a list of channels for this site
     *
     * @Rest\View(
     *      serializerGroups={"channel.summary", "user.summary"}
     * )
     *
     * @param int $id
     * @return \Tagcade\Model\Core\ChannelInterface[]
     */
    public function getChannelsAction($id)
    {
        /** @var SiteInterface $site */
        $site = $this->one($id);

        return $site->getChannels();
    }

    /**
     * get all Sites which have no Ad Slot references to a library Ad Slot
     *
     * @Rest\View(
     *      serializerGroups={"site.detail", "user.summary"}
     * )
     *
     * @Rest\Get("sites/noreference")
     *
     * @Rest\QueryParam(name="slotLibrary", requirements="\d+")
     *
     * @return array
     */
    public function getSitesUnreferencedToLibraryAdSlotAction()
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

        return $this->get('tagcade.domain_manager.site')->getSitesUnreferencedToLibraryAdSlot($slotLibrary);
    }

    protected function getResourceName()
    {
        return 'site';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_site';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.site');
    }
}
