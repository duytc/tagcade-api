<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform\SiteReportInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\AbstractReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;

class Account extends AbstractReportType implements CalculatedReportTypeInterface
{
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