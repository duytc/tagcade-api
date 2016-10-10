<?php

namespace Tagcade\Model\Report\HeaderBiddingReport\Hierarchy\Platform;

use Tagcade\Model\Report\HeaderBiddingReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\HeaderBiddingReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Report\HeaderBiddingReport\AbstractCalculatedReport;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    use SuperReportTrait;

    /** @var UserEntityInterface */
    protected $publisher;

    /**
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function getPublisherId()
    {
        if ($this->publisher instanceof UserEntityInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    public function getPublisherName()
    {
        if ($this->publisher instanceof UserEntityInterface) {
            return $this->publisher->getUsername();
        }

        return null;
    }
    /**
     * @param PublisherInterface $publisher
     * @return $this
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher->getUser();

        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof PlatformReportInterface;
    }

    protected function setDefaultName()
    {
        $this->setName($this->publisher->getUsername());
    }
}