<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PublisherReportType;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;

interface PublisherInterface extends CreatorInterface
{
    /**
     * @param PublisherReportType $reportType
     * @return AccountReportInterface
     */
    public function doCreateReport(PublisherReportType $reportType);
}