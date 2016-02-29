<?php

namespace Tagcade\Service\Report\RtbReport\Creator;


use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;

interface RtbSnapshotReportCreatorInterface extends RtbReportCreatorInterface
{
    /**
     * @inheritdoc
     */
    public function setDate(\DateTime $date);

    /**
     * @inheritdoc
     */
    public function getDate();

    /**
     * @inheritdoc
     */
    public function getEventCounter();

    /**
     * @inheritdoc
     */
    public function getReport(ReportTypeInterface $reportType);
} 