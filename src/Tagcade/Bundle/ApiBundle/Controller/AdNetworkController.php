<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

/**
 * @Rest\RouteResource("Adnetwork")
 */
class AdNetworkController extends RestControllerAbstract implements ClassResourceInterface
{
    /**
     * Get all ad networks
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdNetworkInterface[]
     */
    public function cgetAction()
    {
        return $this->all();
    }

    /**
     * Get a single ad network for the given id
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
     * @return \Tagcade\Model\Core\AdNetworkInterface
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getAction($id)
    {
        return $this->one($id);
    }

    /**
     * Get all sites that have ad tag belonging to this ad network
     *
     * @ApiDoc(
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      404 = "Returned when the resource is not found"
     *  }
     * )
     *
     * @param $id
     * @return SiteInterface[]
     *
     * @throws NotFoundHttpException when the resource does not exist
     */
    public function getSitesAction($id)
    {
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);

        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        return $this->get('tagcade.domain_manager.site')->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);
    }

    /**
     * Create a ad network from the submitted data
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
     * Update an existing ad network from the submitted data or create a new ad network
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
     *
     *
     * Update revenue for ad network.
     *
     * @ApiDoc(
     *  section = "adNetworks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", requirements="\d+", description="Cpm rate of ad network")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return View
     */
    public function putEstcpmAction($id)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $dateUtil = $this->get('tagcade.service.date_util');

        $estCpm = (float)$paramFetcher->get('estCpm');
        if ($estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be positive value');
        }

        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.service.revenue_editor')->updateRevenueForAdNetwork(
            $adNetwork,
            $estCpm,
            $startDate,
            $endDate
        );

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update active state of all at tags belonging to adNetwork $id and $siteId
     *
     * @ApiDoc(
     *  section = "adNetworks",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="active", requirements="\d+", description="active status of site")
     *
     * @param $id
     * @param $siteId
     *
     * @return View
     */
    public function putSiteStatusAction($id, $siteId)
    {
        $paramFetcher = $this->get('fos_rest.request.param_fetcher');
        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        $site = $this->get('tagcade.domain_manager.site')->find($siteId);
        if (!$site) {
            throw new NotFoundHttpException('That site does not exist');
        }

        if (false === $this->get('security.context')->isGranted('edit', $adNetwork) || false === $this->get('security.context')->isGranted('edit', $site)) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $active = $paramFetcher->get('active', true) != 0 ? true : false;

        $this->get('tagcade_app.service.core.ad_network.ad_network_service')->updateActiveStateBySingleSiteForAdNetwork($adNetwork, $site, $active);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    /**
     * Update an existing ad network from the submitted data or create a new ad network at a specific location
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
     * Delete an existing ad network
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

    protected function getResourceName()
    {
        return 'adnetwork';
    }

    protected function getGETRouteName()
    {
        return 'api_1_get_adnetwork';
    }

    protected function getHandler()
    {
        return $this->container->get('tagcade_api.handler.ad_network');
    }
}
