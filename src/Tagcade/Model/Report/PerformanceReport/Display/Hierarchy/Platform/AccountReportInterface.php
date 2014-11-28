<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SuperReportInterface;
use Tagcade\Model\User\UserEntityInterface;

interface AccountReportInterface extends BillableInterface, CalculatedReportInterface, SuperReportInterface, SubReportInterface
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
