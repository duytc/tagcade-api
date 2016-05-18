<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;

interface SiteReportInterface extends CalculatedReportInterface, SubReportInterface, SuperReportInterface
{
    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @return int|null
     */
    public function getSiteId();
}