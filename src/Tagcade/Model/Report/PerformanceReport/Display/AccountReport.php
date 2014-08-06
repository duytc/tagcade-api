<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AccountReport extends AbstractCalculatedReportWithSuper implements AccountReportInterface
{
    protected $publisher;

    /**
     * @return UserEntityInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @param PublisherInterface $publisher
     * @return self
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

    protected function setDefaultName()
    {
        if ($publisher = $this->getPublisher()) {
            $this->setName($publisher->getUsername());
        }
    }
}