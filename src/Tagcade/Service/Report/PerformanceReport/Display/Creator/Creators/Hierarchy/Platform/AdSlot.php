<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
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

    /** @var BillingConfigurationRepositoryInterface */
    private $billingConfigurationRepository;

    public function __construct(AdTagInterface $subReportCreator, BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        $this->subReportCreator = $subReportCreator;
        $this->billingCalculator = $billingCalculator;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
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
            ->setRefreshedSlotOpportunities($this->eventCounter->getSlotOpportunityRefreshesCount($adSlot->getId()))
        ;

        $adSlotReport->setInBannerRequests($this->eventCounter->getInBannerRequestCount($adSlot->getId()));
        $adSlotReport->setInBannerImpressions($this->eventCounter->getInBannerImpressionCount($adSlot->getId()));
        $adSlotReport->setInBannerTimeouts($this->eventCounter->getInBannerTimeoutCount($adSlot->getId()));

        $totalAdTagOpp = 0;
        foreach ($adSlot->getAdTags() as $adTag) {
            $adTagReport = $this->subReportCreator->createReport(new AdTagReportType($adTag))->setSuperReport($adSlotReport);
            $adSlotReport->addSubReport($adTagReport);

            $totalAdTagOpp +=  $adTagReport->getAdOpportunities();
        }

        $adSlotReport->setAdOpportunities($totalAdTagOpp);

        // BillAmount is computed from adSlot opportunity or ad impression opportunity therefor BillAmount must be calculated after adTags report
        $adSlotReport = $this->setBillAmountForAdSlotReport($adSlotReport, $adSlot);

        return $adSlotReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AdSlotReportType;
    }

    /**
     * @param AdSlotReportInterface $adSlotReport
     * @param ReportableAdSlotInterface|NativeAdSlotInterface $adSlot
     * @return AdSlotReportInterface
     */
    private function setBillAmountForAdSlotReport(AdSlotReportInterface $adSlotReport, $adSlot){
        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($adSlotReport->getAdSlot()->getSite()->getPublisher(), User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $adSlotReport->getAdOpportunities();
        } else {
            $weight = $adSlotReport->getSlotOpportunities();
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $weight);

        $adSlotReport->setBilledAmount($rateAmount->getAmount());
        $adSlotReport->setBilledRate($rateAmount->getRate()->getCpmRate());

        if ($rateAmount->getRate()->isCustom()) {
            $adSlotReport->setCustomRate($rateAmount->getRate()->getCpmRate());
        }

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($this->getDate(), $adSlot->getSite()->getPublisher(), $adSlotReport->getInBannerImpressions());

        $adSlotReport->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $adSlotReport->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());

        return $adSlotReport;
    }
}