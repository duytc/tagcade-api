<?php

namespace Tagcade\Service\Report\RtbReport\Creator\Creators\Hierarchy\Impression;


use Tagcade\Model\Report\RtbReport\Hierarchy\AccountReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\Hierarchy\Account as AccountReportType;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbSnapshotCreatorInterface;

interface RtbAccountSnapshotInterface extends RtbSnapshotCreatorInterface
{
    /**
     * @param AccountReportType $reportType
     * @return AccountReportInterface
     */
    public function doCreateReport(AccountReportType $reportType);
}