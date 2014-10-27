<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    use SuperReportTrait;

    /**
     * @var UserEntityInterface
     */
    protected $publisher;

    /**
     * @return UserEntityInterface
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
        if ($this->publisher instanceof PublisherInterface) {
            return $this->publisher->getId();
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
        if ($this->publisher instanceof UserEntityInterface) {
            $this->setName($this->publisher->getUsername());
        }
    }
}