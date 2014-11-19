<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\BaseAdTagReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;

class RevenueEditor implements RevenueEditorInterface {

    /**
     * @var ReportSelectorInterface
     */
    private $reportSelector;
    /**
     * @var EstCpmCalculatorInterface
     */
    private $revenueCalculator;
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ReportSelectorInterface $reportSelector, EstCpmCalculatorInterface $revenueCalculator, ObjectManager $om)
    {
        $this->reportSelector = $reportSelector;
        $this->revenueCalculator = $revenueCalculator;
        $this->om = $om;
    }

    /**
     * @inheritdoc
     */
    public function updateRevenueForAdTag(AdTagInterface $adTag, $cpmRate, DateTime $startDate, DateTime $endDate = null)
    {
        if( !is_numeric($cpmRate)) {
            throw new InvalidArgumentException('CpmRate must be a float number');
        }

        $baseReportTypes = [
            new Platform\AdTag($adTag),
            new AdNetwork\AdTag($adTag),
        ];

        $rootReports = [];

        foreach($baseReportTypes as $reportType) {
            $reports = $this->reportSelector->getReports($reportType, $startDate, $endDate);

            foreach($reports as $report) {
                if (!$report instanceof BaseAdTagReportInterface) {
                    throw new LogicException('Expected an AdTagReport');
                }

                $report->setEstCpm($cpmRate);

                $root = $this->getRootReport($report);

                if (!in_array($root, $rootReports, true)) {
                    $rootReports[] = $root;
                }

                unset($root);
            }
        }

        unset($report);

        foreach ($rootReports as $report) {
            if (!$report instanceof RootReportInterface) {
                throw new LogicException('Expected a RootReportInterface');
            }

            // very important, must be called manually
            // we should move this to Doctrine PrePersist and PreUpdate events
            $report->setCalculatedFields();

            $this->om->persist($report);
        }

        $this->om->flush();

        return $this;
    }

    /**
     * @param BaseAdTagReportInterface $adTagReport
     * @return RootReportInterface;
     */
    protected function getRootReport(BaseAdTagReportInterface $adTagReport)
    {
        $current = $adTagReport;

        // Loop 10 times to prevent infinite loop due to programming mistake
        for($i = 0; $i < 10; $i ++) {
            $current = $current->getSuperReport();
            if($current instanceof RootReportInterface) {
                break;
            }
        }

        if(!$current instanceof RootReportInterface) {
            throw new LogicException('Expected RootReportInterface');
        }

        return $current;
    }

    /**
     * @inheritdoc
     */
    public function updateRevenueForAdNetwork(AdNetworkInterface $adNetwork, $cpmRate, DateTime $startDate, DateTime $endDate = null)
    {
        $adTags = $adNetwork->getAdTags();

        foreach($adTags as $adTag) {
            $this->updateRevenueForAdTag($adTag, $cpmRate, $startDate, $endDate);
        }

        return $this;
    }
} 