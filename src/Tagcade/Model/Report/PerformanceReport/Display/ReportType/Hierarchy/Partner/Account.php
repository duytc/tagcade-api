<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner;

use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\AbstractCalculatedReportType;
use Tagcade\Model\User\Role\PublisherInterface;

class Account extends AbstractCalculatedReportType
{
    const REPORT_TYPE = 'partner.account';

    /** @var PublisherInterface */
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

    public function getPublisherId()
    {
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
        }

        return null;
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
        return false; // not supported
    }
}