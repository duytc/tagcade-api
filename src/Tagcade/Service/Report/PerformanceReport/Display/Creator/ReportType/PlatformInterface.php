<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Model\Report\PerformanceReport\Display\PlatformReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface PlatformInterface extends ReportTypeInterface
{
    /**
     * @param PublisherInterface[] $publishers
     * @return PlatformReportInterface
     */
    public function doCreateReport(array $publishers);
}