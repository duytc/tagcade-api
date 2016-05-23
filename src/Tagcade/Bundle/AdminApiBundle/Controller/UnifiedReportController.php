<?php

namespace Tagcade\Bundle\AdminApiBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tagcade\Entity\Core\AdNetworkPartner;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class UnifiedReportController extends FOSRestController
{
    const PARAMS_PUBLISHER = 'publisher';
    const PARAMS_PARTNER_CNAME = 'partner';
    const PARAMS_REPORTS = 'reports';
    const PARAMS_OVERRIDE = 'override';
    const PARAMS_START_DATE = 'startDate';
    const PARAMS_END_DATE = 'endDate';
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

        $reportDateRange = $this->get('tagcade_app.service.unified_report.report_importer')->importReports(
            $adNetwork,
            $reports,
            $override
        );

        return $reportDateRange;

        if ($reportDateRange === false || !is_array($reportDateRange)) {
            return $this->view('no date range returned after import', 204);
        }

        if (!array_key_exists('startDate', $reportDateRange) || !array_key_exists('endDate', $reportDateRange)) {
            return $this->view('not startDate and endDate key in array returned', 204);

        }
        //command to run
        $startDate = $reportDateRange['startDate'];
        $endDate = $reportDateRange['endDate'];
        $command = sprintf('tc:unified-report:compare --publisher=%d --start-date=%s --end-date=%s --override', $publisherId, $startDate, $endDate);

        $commandToRun = sprintf('%s %s', $this->getAppConsoleCommand(), $command);
        $process = new Process($commandToRun);
        $process->start();

        return $this->view(null, 204);
    }


    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getParameter('kernel.root_dir');

        return sprintf('php %s/console', $pathToSymfonyConsole);
    }


    /**
     * @Rest\Post("/unifiedreports/compare")
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
    public function createComparisonReportsAction(Request $request)
    {
        $logger = $this->get('logger');
        $data = $request->request->all();
        $publisherId = intval($data[self::PARAMS_PUBLISHER]);
        $publisher = $this->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            $logger->error(sprintf('The publisher %d does not exist', $publisherId));
            throw new InvalidArgumentException(sprintf('The publisher %d does not exist', $publisherId));
        }

        $startDate = $data[self::PARAMS_START_DATE];
        $endDate = $data[self::PARAMS_END_DATE];

        if (!preg_match('/\d{4}-\d{2}-\d{2}/', $startDate) || !preg_match('/\d{4}-\d{2}-\d{2}/', $endDate)) {
            $logger->error(sprintf('Either %s or %s is a invalid date format, try "YYYY-MM-DD" instead', $startDate, $endDate));
            throw new InvalidArgumentException(sprintf('Either %s or %s is a invalid date format, try "YYYY-MM-DD" instead', $startDate, $endDate));
        }

        $override = filter_var($data[self::PARAMS_OVERRIDE], FILTER_VALIDATE_BOOLEAN);

        $reportComparisonCreator = $this->get('tagcade.service.report.unified_report.report_comparison_creator');
        try {
            $reportComparisonCreator->updateComparisonForPublisher($publisher, new \DateTime($startDate), new \DateTime($endDate), $override);
            return TRUE;
        } catch (UniqueConstraintViolationException $ex) {
            $logger->error('Some data might have been created before. Use option "--override" instead');
            throw new RuntimeException('Some data might have been created before. Use option "--override" instead');
        }
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
