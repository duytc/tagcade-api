<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\RtbReport\SuperReportInterface;
use Tagcade\Model\Report\RtbReport\SubReportInterface;

interface SiteReportInterface extends SuperReportInterface, SubReportInterface
{
    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @return int|null
     */
    public function getSiteId();

    /**
     * @param SiteInterface|null $site
     * @return $this
     */
    public function setSite($site);
}
