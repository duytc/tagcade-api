<?php

namespace Tagcade\Model\Report\RtbReport\ReportType\Hierarchy;

use Tagcade\Model\Report\RtbReport\Hierarchy\AccountReportInterface;
use Tagcade\Model\Report\RtbReport\Hierarchy\SiteReportInterface;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\Report\RtbReport\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;

class Account extends AbstractCalculatedReportType implements CalculatedReportTypeInterface
{
    const REPORT_TYPE = 'account';

    /**
     * @var PublisherInterface
     */
    private $publisher;

    public function __construct(PublisherInterface $publisher)
    {

        $this->publisher = $publisher;
    }

    /**
     * @return PublisherInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return int|null
     */
    public function getPublisherId()
    {
        return $this->publisher->getId();
    }

    /**
     * @inheritdoc
     */
    public function matchesReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    /**
     * @inheritdoc
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }
}