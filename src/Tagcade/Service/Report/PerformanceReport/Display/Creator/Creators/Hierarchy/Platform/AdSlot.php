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

        $adSlotReport = new AdSlotReport();

        /** @var AdSlotReportType $reportType */
        $adSlot = $reportType->getAdSlot();

        $adSlotReport
            ->setAdSlot($adSlot)
            ->setDate($this->getDate())
            ->setSlotOpportunities($this->eventCounter->getSlotOpportunityCount($adSlot->getId()))
        ;

        if ($adSlot instanceof DisplayAdSlotInterface) {
            $adSlotReport->setInBannerRequests($this->eventCounter->getInBannerRequestCount($adSlot->getId()));
            $adSlotReport->setInBannerImpressions($this->eventCounter->getInBannerImpressionCount($adSlot->getId()));
            $adSlotReport->setInBannerTimeouts($this->eventCounter->getInBannerTimeoutCount($adSlot->getId()));
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $adSlotReport->getSlotOpportunities());

        $adSlotReport->setBilledAmount($rateAmount->getAmount());
        $adSlotReport->setBilledRate($rateAmount->getRate()->getCpmRate());

        if ($rateAmount->getRate()->isCustom()) {
            $adSlotReport->setCustomRate($rateAmount->getRate()->getCpmRate());
        }

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $adSlotReport->getInBannerImpressions());

        $adSlotReport->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $adSlotReport->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());

        foreach ($adSlot->getAdTags() as $adTag) {
            $adSlotReport->addSubReport(
                $this->subReportCreator->createReport(new AdTagReportType($adTag))
                    ->setSuperReport($adSlotReport)
            );
        }

        return $adSlotReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }
}