<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\BaseAdTagReportInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
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
    /**
     * @var AdTagRepositoryInterface
     */
    private $adTagRepository;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct(ReportSelectorInterface $reportSelector, EstCpmCalculatorInterface $revenueCalculator, ObjectManager $om, AdTagRepositoryInterface $adTagRepository)
    {
        $this->reportSelector = $reportSelector;
        $this->revenueCalculator = $revenueCalculator;
        $this->om = $om;
        $this->adTagRepository = $adTagRepository;
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
        $params = new Params($startDate, $endDate);

        $this->writeln(sprintf("%s START updating revenue for ad tag '%s' in ad slot '%s' in site '%s'... from Date %s to Date %s\n",
                    date('c'), $adTag->getName(), $adTag->getAdSlot()->getName(), $adTag->getAdSlot()->getSite()->getName(), $startDate->format('Y-m-d'), $endDate->format('Y-m-d')));

        // Step 1. Update cpm in WaterfallTag report (base of calculation for AdSlot, Site, Account and Platform report
        foreach($baseReportTypes as $reportType) {
            $reports = $this->reportSelector->getReports($reportType, $params);

            if (false === $reports) {
                continue; // not found reports
            }

            foreach($reports->getReports() as $report) {
                if (!$report instanceof BaseAdTagReportInterface) {
                    throw new LogicException('Expected an WaterfallTagReport');
                }

                $report->setEstCpm($cpmRate);
                $root = $this->getRootReport($report);

                if (!in_array($root, $rootReports, true)) {
                    $rootReports[] = $root;
                }

                unset($root);
                unset($report);
            }
        }

        unset($report);

        // Step 2. update calculated fields from top level (Platform) to sub level (Account, Site, AdSlot, Site)
        foreach ($rootReports as $report) {
            /**
             * @var RootReportInterface $report
             */

            $this->writeln(sprintf("%s updating report '%s' on Date %s\n", date('c'), $report->getName(), $report->getDate()->format('Y-m-d')));

            // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
            /**
             * @var RootReportInterface $report
             */
            $report->setCalculatedFields();

            // Step 3. Update database
            $this->om->flush();
            $this->om->detach($report);

            $this->writeln(sprintf("%s finish updating report '%s' on Date %s\n", date('c'), $report->getName(), $report->getDate()->format('Y-m-d')));

            unset($report);

            gc_collect_cycles();

        }

        $this->writeln(sprintf("%s FINISH updating revenue for ad tag '%s' in ad slot '%s' in site '%s'... from Date %s to Date %s\n",
                    date('c'), $adTag->getName(), $adTag->getAdSlot()->getName(), $adTag->getAdSlot()->getSite()->getName(), $startDate->format('Y-m-d'), $endDate->format('Y-m-d')));

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

    public function updateRevenueForAdNetworkSite(AdNetworkInterface $adNetwork, SiteInterface $site, $cpmRate, DateTime $startDate, DateTime $endDate = null)
    {
        $adTags = $this->adTagRepository->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        foreach($adTags as $adTag) {
            $this->updateRevenueForAdTag($adTag, $cpmRate, $startDate, $endDate);
        }

        return $this;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    protected function hasOutput()
    {
        return null !== $this->output;
    }

    protected function writeln($line)
    {
        if ($this->hasOutput()) {
            $this->output->writeln($line);
        }
    }
} 