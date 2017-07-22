<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Segment;

use Tagcade\Entity\Report\PerformanceReport\Display\Segment\RonAdSlotReport;
use Tagcade\Model\Core\SegmentInterface as SegmentModelInterface;
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

    public function __construct(RonAdTagInterface $subReportCreator, BillingCalculatorInterface $billingCalculator)
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

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($this->getDate(), $ronAdSlot->getLibraryAdSlot()->getPublisher(), $report->getSlotOpportunities());
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