<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SubReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SuperReportInterface;

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
