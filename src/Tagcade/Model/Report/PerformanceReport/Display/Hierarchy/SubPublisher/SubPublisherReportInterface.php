<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\CalculatedReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

interface SubPublisherReportInterface extends CalculatedReportInterface, RootReportInterface, PartnerReportInterface
{
    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher();

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher);
}