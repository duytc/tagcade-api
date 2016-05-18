<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;

use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AccountReport extends AbstractCalculatedReport implements AccountReportInterface
{
    use PartnerReportFields;

    protected $id;

    protected $date;
    protected $name;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;

    /** @var PublisherInterface */
    protected $publisher;

    /**
     * @inheritdoc
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }
}