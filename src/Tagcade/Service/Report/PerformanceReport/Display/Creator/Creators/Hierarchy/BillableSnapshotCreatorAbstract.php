<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy;


use Tagcade\Bundle\UserSystem\AdminBundle\Entity\User;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;

abstract class BillableSnapshotCreatorAbstract extends SnapshotCreatorAbstract
{
    /** @var BillingCalculatorInterface */
    protected $billingCalculator;

    /** @var BillingConfigurationRepositoryInterface */
    protected $billingConfigurationRepository;

    function __construct(BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
    {
        $this->billingCalculator = $billingCalculator;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
    }

    public function parseRawReportData(ReportInterface $report, array $redisReportData)
    {
        if (!$report instanceof CalculatedReportInterface) {
            throw new InvalidArgumentException('Expect instance of CalculatedReportInterface');
        }

        parent::parseRawReportData($report, $redisReportData);

        if ($report instanceof AdSlotReportInterface) {
            $publisher = $report->getAdSlot()->getSite()->getPublisher();
        } else if ($report instanceof SiteReportInterface) {
            $publisher = $report->getSite()->getPublisher();
        } else if ($report instanceof AccountReportInterface) {
            $publisher = $report->getPublisher();
        } else if ($report instanceof RonAdSlotReportInterface) {
            $publisher = $report->getRonAdSlot()->getLibraryAdSlot()->getPublisher();
        } else {
            throw new LogicException('Billable Creator should be AdSlot, Site and Account report');
        }

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, User::MODULE_DISPLAY);
        if (!$billingConfiguration instanceof BillingConfigurationInterface) {
            $billingConfiguration = new BillingConfiguration();
            $billingConfiguration->setBillingFactor(BillingConfiguration::BILLING_FACTOR_SLOT_OPPORTUNITY);
        }
        
        $billingFactor = $billingConfiguration->getBillingFactor();
        if ($billingFactor == BillingConfiguration::BILLING_FACTOR_IMPRESSION_OPPORTUNITY) {
            $weight = $report->getAdOpportunities();
        } else {
            $weight = $report->getSlotOpportunities();
        }

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($report->getDate(), $publisher, $weight);

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateInBannerBilledAmountForPublisher($report->getDate(), $publisher, $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }
}