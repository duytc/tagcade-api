<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Report\PerformanceReport\Display\BaseAdTagReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;

class RevenueEditor implements RevenueEditorInterface
{
    use GetRootReportTrait;
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

        $today = new DateTime('today');

        if(!$endDate) {
            $endDate = $startDate;
        }

        if ($startDate >= $today || $endDate >= $today ) {
            throw new InvalidArgumentException('Can only update revenue information for reports older than today');
        }

        $baseReportTypes = [
            new Platform\AdTag($adTag),
            new AdNetwork\AdTag($adTag),
        ];

        $rootReports = [];

        // Step 1. Update cpm in AdTag report (base of calculation for AdSlot, Site, Account and Platform report
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

        // Step 2. update calculated fields from top level (Platform) to sub level (Account, Site, AdSlot, Site)
        foreach ($rootReports as $report) {
            // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
            /**
             * @var RootReportInterface $report
             */
            $report->setCalculatedFields();
        }

        // Step 3. Update database
        $this->om->flush();

        return $this;
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