<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy;


use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Segment\RonAdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\SnapshotCreatorAbstract;

abstract class BillableSnapshotCreatorAbstract extends SnapshotCreatorAbstract
{
    /** @var BillingCalculatorInterface */
    private $billingCalculator;

    function __construct(BillingCalculatorInterface $billingCalculator)
    {
        $this->billingCalculator = $billingCalculator;
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

        $rateAmount = $this->billingCalculator->calculateTodayBilledAmountForPublisher($report->getDate(), $publisher, AbstractUser::MODULE_DISPLAY, $report->getSlotOpportunities());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        $inBannerRateAmount = $this->billingCalculator->calculateTodayInBannerBilledAmountForPublisher($publisher, AbstractUser::MODULE_IN_BANNER, $report->getInBannerImpressions());

        $report->setInBannerBilledAmount($inBannerRateAmount->getAmount());
        $report->setInBannerBilledRate($inBannerRateAmount->getRate()->getCpmRate());
    }
}