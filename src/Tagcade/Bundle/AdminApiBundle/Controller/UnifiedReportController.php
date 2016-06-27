<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;

class UnifiedReportController extends FOSRestController
{
    const PARAMS_PUBLISHER = 'publisher';
    const PARAMS_PARTNER_CNAME = 'partner';
    const PARAMS_REPORTS = 'reports';
    const PARAMS_OVERRIDE = 'override';
    const PARAMS_START_DATE = 'start-date';
    const PARAMS_END_DATE = 'end-date';
    /**
     * @Rest\Post("/unifiedreports/import")
     *
     * @ApiDoc(
     *  section = "Unified Report Importer",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param Request $request
     * @return array
     */
    public function importUnifiedReportsAction(Request $request)
    {
        $data = $request->request->all();
        $publisherId = intval($data[self::PARAMS_PUBLISHER]);
        $partnerCName = $data[self::PARAMS_PARTNER_CNAME];
        $startDate = $data[self::PARAMS_START_DATE];
        $endDate = $data[self::PARAMS_END_DATE];
        $override = filter_var($data[self::PARAMS_OVERRIDE], FILTER_VALIDATE_BOOLEAN);
        $reports = $data[self::PARAMS_REPORTS];
        $reports = json_decode($reports, true);

        if ($publisherId < 1 || empty($partnerCName)) {
            throw new InvalidArgumentException('either publisher id or partner canonical name is invalid');
        }

        $adNetwork = $this->get('tagcade.repository.ad_network')->getAdNetworkByPublisherAndPartnerCName($publisherId, $partnerCName);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException('either publisher id or partner canonical name is invalid or they do not work together');
        }

        $this->get('tagcade_app.service.unified_report.report_importer')->importReports(
            $adNetwork,
            $reports,
            $override
        );

        //update comparison by worker
        $override = true;

        $this->get('tagcade.worker.manager')->updateComparisonForPublisher($publisherId, $startDate, $endDate, $override);

        return $this->view(null, 204);
    }


    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getParameter('kernel.root_dir');

        return sprintf('php %s/console', $pathToSymfonyConsole);
    }

    /**
     * @Rest\Get("/unifiedreports/partners")
     *
     * @ApiDoc(
     *  section = "Unified Report Importer",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @return array
     */
    public function getListPartnerAction()
    {
        return $this->get('tagcade.repository.ad_network_partner')->findAll();
    }

    /**
     * @Rest\Post("/unifiedreports/partners")
     *
     * @ApiDoc(
     *  section = "Unified Report Importer",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param Request $request
     * @return array
     */
    public function createPartnerAction(Request $request)
    {
        $data = $request->request->all();
        if (!isset($data['name']) || empty($data['name'])) {
            throw new InvalidArgumentException('name is mandatory');
        }

        $name = strtolower(str_replace(' ', '', $data['name']));
        $partnerManager = $this->get('tagcade.domain_manager.ad_network_partner');
        $partner = $partnerManager->getByCanonicalName($name);
        if ($partner instanceof AdNetworkPartnerInterface) {
            throw new InvalidArgumentException(sprintf('partner %s already existed', $data['name']));
        }

        $partner = new AdNetworkPartner();
        $partner->setName($data['name']);
        if (isset($data['url'])) {
            $partner->setUrl($data['url']);
        }

        $partnerManager->save($partner);

        return true;
    }

    /**
     * @Rest\Delete("/unifiedreports/partners/{partnerId}", requirements={"partnerId" = "\d+"})
     *
     * @ApiDoc(
     *  section = "Unified Report Importer",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful",
     *      400 = "There's no report for that query"
     *  }
     * )
     *
     * @param $partnerId
     * @return array
     */
    public function removePartnerAction($partnerId)
    {
        $partnerManager = $this->get('tagcade.domain_manager.ad_network_partner');
        $partner = $partnerManager->find($partnerId);
        if (!$partner instanceof AdNetworkPartnerInterface) {
            throw new InvalidArgumentException(sprintf('partner %s does not exist'));
        }

        $partnerManager->delete($partner);

        return true;
    }
}
