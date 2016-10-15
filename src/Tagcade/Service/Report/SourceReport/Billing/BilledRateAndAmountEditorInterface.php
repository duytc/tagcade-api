<?php

namespace Tagcade\Service\Report\SourceReport\Billing;

use DateTime;
use Tagcade\Model\User\Role\PublisherInterface;

interface BilledRateAndAmountEditorInterface
{
    /**
     * @param DateTime $date
     * @param PublisherInterface $publisher
     */
    public function updateBilledRateAndBilledAmountSourceReportForPublisher(PublisherInterface $publisher, DateTime $date);
}