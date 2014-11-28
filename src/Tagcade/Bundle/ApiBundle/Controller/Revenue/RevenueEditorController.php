<?php

namespace Tagcade\Bundle\ApiBundle\Controller\Revenue;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class RevenueEditorController extends FOSRestController
{

    /**
     *
     * @Rest\Put("/adTags/{id}/estCpm", requirements={"id" = "\d+"})
     *
     * Update revenue for ad tag.
     *
     * @ApiDoc(
     *  section = "adTags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", requirements="\d+", description="Cpm rate of adTag")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return int
     */
    public function putRevenueForAdTagAction($id)
    {
        $paramFetcher   = $this->get('fos_rest.request.param_fetcher');
        $dateUtil       = $this->get('tagcade.service.date_util');

        $estCpm = (float)$paramFetcher->get('estCpm');
        if (!is_numeric($estCpm) || $estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be nummeric and positive value');
        }

        $adTag = $this->get('tagcade.domain_manager.ad_tag')->find($id);
        if (!$adTag) {
            throw new NotFoundHttpException('That adTag does not exist');
        }

        if ( false === $this->get('security.context')->isGranted('edit', $adTag) ) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate  = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate    = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.service.revenue_editor')->updateRevenueForAdTag($adTag, $estCpm, $startDate, $endDate);
    }

    /**
     *
     * @Rest\Put("/adNetworks/{id}/estCpm", requirements={"id" = "\d+"})
     *
     * Update revenue for ad network.
     *
     * @ApiDoc(
     *  section = "adNetworks",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="estCpm", requirements="\d+", description="Cpm rate of ad network")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return int
     */
    public function putRevenueForAdNetworkAction($id)
    {
        $paramFetcher   = $this->get('fos_rest.request.param_fetcher');
        $dateUtil       = $this->get('tagcade.service.date_util');

        $estCpm = (float)$paramFetcher->get('estCpm');
        if ($estCpm < 0) {
            throw new InvalidArgumentException('estCpm should be positive value');
        }

        $adNetwork = $this->get('tagcade.domain_manager.ad_network')->find($id);
        if (!$adNetwork) {
            throw new NotFoundHttpException('That adNetwork does not exist');
        }

        if ( false === $this->get('security.context')->isGranted('edit', $adNetwork) ) {
            throw new AccessDeniedException('You do not have permission to edit this');
        }

        $startDate = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate   = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->get('tagcade.service.revenue_editor')->updateRevenueForAdNetwork($adNetwork, $estCpm, $startDate, $endDate);
    }


} 