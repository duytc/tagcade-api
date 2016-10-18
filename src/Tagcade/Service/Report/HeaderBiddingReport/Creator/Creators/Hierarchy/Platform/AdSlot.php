<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;
use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\AdSlotReport;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\AdSlot as AdSlotReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;

class AdSlot extends CreatorAbstract implements AdSlotInterface
{
    /**
     * @var BillingCalculatorInterface
     */
    private $billingCalculator;

    public function __construct(BillingCalculatorInterface $billingCalculator)
    {
        $this->billingCalculator = $billingCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AdSlotReportType $reportType)
    {
        $report = new AdSlotReport();
        $adSlot = $reportType->getAdSlot();

        $report
            ->setAdSlot($adSlot)
            ->setDate($this->getDate())
            ->setRequests($this->eventCounter->getHeaderBidRequestCount($adSlot->getId()))
        ;

        $rateAmount = $this->billingCalculator->calculateTodayHbBilledAmountForPublisher($adSlot->getSite()->getPublisher(), AbstractUser::MODULE_HEADER_BIDDING, $report->getRequests());

        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

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