<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\SubReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\SuperReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\User\UserEntityInterface;

interface AccountReportInterface extends SuperReportInterface, SubReportInterface, ReportInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();
}
