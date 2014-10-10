<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\PerformanceReport\Display\AccountReportInterface;

interface AccountInterface extends ReportTypeInterface
{
    /**
     * @param PublisherInterface $publisher
     * @return AccountReportInterface
     */
    public function doCreateReport(PublisherInterface $publisher);
}