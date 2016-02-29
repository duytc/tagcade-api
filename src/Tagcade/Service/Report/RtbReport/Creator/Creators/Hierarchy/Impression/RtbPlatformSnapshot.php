<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Entity\Report\RtbReport\PlatformReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\RtbReport\Hierarchy\AccountReport;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Account as AccountReportType;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Platform as PlatformReportType;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorAbstract;

class RtbPlatformSnapshot extends RtbSnapshotCreatorAbstract implements RtbPlatformSnapshotInterface
{
    /**
     * @var RtbAccountSnapshot
     */
    private $rtbAccountSnapshotCreator;

    public function __construct(RtbAccountSnapshot $rtbAccountSnapshotCreator)
    {
        $this->rtbAccountSnapshotCreator = $rtbAccountSnapshotCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(PlatformReportType $reportType)
    {
        $platformReport = new PlatformReport();

        $platformReport
            ->setDate($this->getDate());

        $result = array(
            self::RESULT_KEY_SLOT_OPPORTUNITY => 0,
            self::RESULT_KEY_IMPRESSION => 0,
            self::RESULT_KEY_PRICE => 0,
        );

        $this->rtbAccountSnapshotCreator->setEventCounter($this->eventCounter);
        $allPublishers = $reportType->getPublishers();

        foreach ($allPublishers as $publisher) {
            /** @var AccountReport $accountReport */
            $accountReport = $this->rtbAccountSnapshotCreator->createReport(new AccountReportType($publisher));
            $result[self::RESULT_KEY_SLOT_OPPORTUNITY] += $accountReport->getOpportunities();
            $result[self::RESULT_KEY_IMPRESSION] += $accountReport->getImpressions();
            $result[self::RESULT_KEY_PRICE] += $accountReport->getEarnedAmount();

            $platformReport->addSubReport($accountReport->setSuperReport($platformReport));
        }

        $this->constructReportModel($platformReport, $result);

        return $platformReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof PlatformReportType;
    }

    protected function constructReportModel(ReportInterface $report, array $data)
    {
        if (!$report instanceof PlatformReport) {
            throw new InvalidArgumentException('Expect instance PlatformReport');
        }

        $report
            ->setOpportunities($data[self::RESULT_KEY_SLOT_OPPORTUNITY])
            ->setImpressions($data[self::RESULT_KEY_IMPRESSION])
            ->setEarnedAmount($data[self::RESULT_KEY_PRICE])
            ->setFillRate();
    }
}