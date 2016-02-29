<?php

namespace Tagcade\Model\Report\RtbReport\Hierarchy;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Report\RtbReport\SubReportInterface;
use Tagcade\Model\Report\RtbReport\SuperReportInterface;

interface AccountReportInterface extends SuperReportInterface, SubReportInterface
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
