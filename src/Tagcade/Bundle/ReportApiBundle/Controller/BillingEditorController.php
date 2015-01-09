<?php

namespace Tagcade\Bundle\ReportApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagcade\Exception\InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\View\View;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class BillingEditorController extends FOSRestController
{
    /**
     *
     * @Rest\Put("/publishers/{id}/billedRate", requirements={"id" = "\d+"})
     *
     * Update billed amount for a publisher.
     *
     * @ApiDoc(
     *  section = "admin",
     *  resource = true,
     *  statusCodes = {
     *      204 = "Returned when successful"
     *  }
     * )
     *
     * @Rest\QueryParam(name="billedRate", requirements="\d+", description="Cpm rate of publisher")
     * @Rest\QueryParam(name="startDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="Date of the cpm in format YYYY-MM-DD, defaults to the today")
     * @Rest\QueryParam(name="endDate", requirements="\d{4}-\d{2}-\d{2}", nullable=true, description="If you want setting in a range, set this to a date in format YYYY-MM-DD - must be older or equal than 'startDate'")
     *
     * @param $id
     *
     * @return View
     */
    public function putBilledRateForPublisherAction($id)
    {
        $paramFetcher   = $this->get('fos_rest.request.param_fetcher');
        $dateUtil       = $this->get('tagcade.service.date_util');

        $billedRate = (float)$paramFetcher->get('billedRate');
        if (!is_numeric($billedRate) || $billedRate < 0) {
            throw new InvalidArgumentException('billedRate should be numeric and non negative value');
        }

        $publisher = $this->get('tagcade_user.domain_manager.publisher')->findPublisher($id);
        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        $startDate  = $dateUtil->getDateTime($paramFetcher->get('startDate'), true);
        $endDate    = $dateUtil->getDateTime($paramFetcher->get('endDate'), true);

        $this->getBillingEditor()->updateHistoricalBilledAmount($publisher, $billedRate, $startDate, $endDate);

        return $this->view(null, Codes::HTTP_NO_CONTENT);
    }

    protected function getBillingEditor()
    {
        return $this->get('tagcade.service.report.performance_report.display.billing.billed_amount_editor');
    }

} 