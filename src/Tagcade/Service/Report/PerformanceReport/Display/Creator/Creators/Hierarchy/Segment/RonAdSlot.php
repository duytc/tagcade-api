<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdSlotReport;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\Core\BillingConfigurationInterface;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdSlot as RonAdSlotReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment\RonAdTag as RonAdTagReportType;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;


class RonAdSlot extends CreatorAbstract implements RonAdSlotInterface
{
    use HasSubReportsTrait;

    /**
     * @var BillingCalculatorInterface
     */
    private $billingCalculator;

    /**
     * @var BillingConfigurationRepositoryInterface
     */
    private $billingConfigurationRepository;

    public function __construct(RonAdTagInterface $subReportCreator, BillingCalculatorInterface $billingCalculator, BillingConfigurationRepositoryInterface $billingConfigurationRepository)
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

        /** @var RonAdSlotReportType $reportType */
        $ronAdSlot = $reportType->getRonAdSlot();
        $segment = $reportType->getSegment();
        $report = new RonAdSlotReport();
        $report
            ->setRonAdSlot($ronAdSlot)
            ->setSegment($segment)
            ->setDate($this->getDate())
            ->setSlotOpportunities($this->eventCounter->getRonSlotOpportunityCount($ronAdSlot->getId(), $segment instanceof SegmentModelInterface ? $segment->getId(): null))
        ;

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($ronAdSlot->getLibraryAdSlot()->getPublisher(), User::MODULE_DISPLAY);

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

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($this->getDate(), $ronAdSlot->getLibraryAdSlot()->getPublisher(), $weight);
        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        if ($rateAmount->getRate()->isCustom()) {
            $report->setCustomRate($rateAmount->getRate()->getCpmRate());
        }

        /** @var \Tagcade\Model\Core\RonAdTagInterface $ronAdTag */
        foreach ($ronAdSlot->getLibraryAdSlot()->getLibSlotTags() as $ronAdTag) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new RonAdTagReportType($ronAdTag, $segment))
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
        return $reportType instanceof RonAdSlotReportType;
    }
}