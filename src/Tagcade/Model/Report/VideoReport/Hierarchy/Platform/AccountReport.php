<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\Fields\SuperReportTrait;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    use SuperReportTrait;

    /** @var UserEntityInterface */
    protected $publisher;

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

    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }


    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof PublisherReport;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDateTime()
    {
        return $this->date;
    }

    public function isValidSuperReport(ReportInterface $report)
    {
        return $report instanceof PlatformReportInterface;
    }
}