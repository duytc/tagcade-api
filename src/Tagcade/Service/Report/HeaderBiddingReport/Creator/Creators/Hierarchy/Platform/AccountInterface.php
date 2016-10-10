<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportInterface;

interface AccountInterface extends CreatorInterface
{
    /**
     * @param AccountReportType $reportType
     * @return AccountReportInterface
     */
    public function doCreateReport(AccountReportType $reportType);
}