<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportInterface;

interface AccountInterface extends CreatorInterface
{
    /**
     * @param AccountReportType $reportType
     * @return AccountReportInterface
     */
    public function doCreateReport(AccountReportType $reportType);
}