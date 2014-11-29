<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Model\User\Role\Publisher;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdSlotReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;

class AdSlot extends CreatorAbstract implements AdSlotInterface
{
    use HasSubReportsTrait;

    /**
     * @var BillingCalculatorInterface
     */
    private $billingCalculator;

    public function __construct(AdTagInterface $subReportCreator, BillingCalculatorInterface $billingCalculator)
    {
        $this->subReportCreator = $subReportCreator;
        $this->billingCalculator = $billingCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdSlotReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new AdSlotReport();

        $adSlot = $reportType->getAdSlot();

        $report
            ->setAdSlot($adSlot)
            ->setDate($this->getDate())
            ->setSlotOpportunities($this->eventCounter->getSlotOpportunityCount($adSlot->getId()));

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher(new Publisher($adSlot->getSite()->getPublisher()), $report->getSlotOpportunities());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate());

        foreach ($adSlot->getAdTags() as $adTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AdTagReportType($adTag))
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }
}