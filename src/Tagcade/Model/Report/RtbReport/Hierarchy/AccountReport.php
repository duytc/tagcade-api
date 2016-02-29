<?php


namespace Tagcade\Model\Report\RtbReport\Hierarchy;


use Tagcade\Model\Report\RtbReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\RtbReport\ReportInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\User\Role\PublisherInterface;

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
        if ($this->publisher instanceof UserEntityInterface) {
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

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof PlatformReportInterface;
    }

    protected function setDefaultName()
    {
        if ($this->publisher instanceof PublisherInterface) {
            $name = null !== $this->publisher->getCompany() ? $this->publisher->getCompany() : $this->publisher->getUsername();
            $this->setName($name);
        }
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof SiteReportInterface;
    }
}