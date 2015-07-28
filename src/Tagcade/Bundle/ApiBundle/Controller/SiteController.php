<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
