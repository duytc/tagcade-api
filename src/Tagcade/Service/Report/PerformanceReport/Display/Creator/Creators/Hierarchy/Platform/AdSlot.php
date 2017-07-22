<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AdSlotReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\AdTag as AdTagReportType;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;

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
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $this->syncEventCounterForSubReports();

        /** @var AdSlotReportType $reportType */
        $report = new AdSlotReport();

        $adSlot = $reportType->getAdSlot();

        $report
            ->setAdSlot($adSlot)
            ->setDate($this->getDate())
            ->setSlotOpportunities($this->eventCounter->getSlotOpportunityCount($adSlot->getId()))
        ;

        if ($adSlot instanceof DisplayAdSlotInterface) {
            $report->setInBannerRequests($this->eventCounter->getInBannerRequestCount($adSlot->getId()));
            $report->setInBannerImpressions($this->eventCounter->getInBannerImpressionCount($adSlot->getId()));
            $report->setInBannerTimeouts($this->eventCounter->getInBannerTimeoutCount($adSlot->getId()));
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $report->getSlotOpportunities());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        if ($rateAmount->getRate()->isCustom()) {
            $report->setCustomRate($rateAmount->getRate()->getCpmRate());
        }

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());

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