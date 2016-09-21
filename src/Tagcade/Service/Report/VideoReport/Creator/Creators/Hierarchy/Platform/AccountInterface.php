<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;

interface AccountInterface extends CreatorInterface
{
    /**
     * @param AccountReportType $reportType
     * @return AccountReportInterface
     */
    public function doCreateReport(AccountReportType $reportType);
}