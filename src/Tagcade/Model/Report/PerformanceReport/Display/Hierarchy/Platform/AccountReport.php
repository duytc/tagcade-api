<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\Fields\SuperReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AggregatePartnerReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    use SuperReportTrait;
    use AggregatePartnerReportTrait;

    /** @var UserEntityInterface */
    protected $publisher;

    protected $subPublisherReports = []; // subPublisherId => SubPublisherReport

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

    protected function postCalculateFields()
    {
        parent::postCalculateFields();
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

    /**
     * @return array
     */
    public function getSubPublisherReports()
    {
        return $this->subPublisherReports;
    }

    protected function setDefaultName()
    {
        if ($this->publisher instanceof PublisherInterface) {
            $name = null !== $this->publisher->getCompany() ? $this->publisher->getCompany() : $this->publisher->getUsername();
            $this->setName($name);
        }
    }
}