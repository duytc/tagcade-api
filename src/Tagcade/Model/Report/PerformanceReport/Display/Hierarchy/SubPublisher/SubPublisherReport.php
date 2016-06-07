<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher;


use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherReport extends AbstractCalculatedReport implements SubPublisherReportInterface
{
    use PartnerReportFields;

    protected $id;

    protected $date;
    protected $name;
    protected $totalOpportunities;
    protected $impressions;
    protected $passbacks;
    protected $fillRate;
    protected $estRevenue;
    protected $estCpm;

    /** @var SubPublisherInterface */
    protected $subPublisher;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function setSubPublisher(SubPublisherInterface $subPublisher)
    {
        $this->subPublisher = $subPublisher;
        return $this;
    }

    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }


    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }

    protected function setDefaultName()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            $this->setName($this->subPublisher->getUser()->getUsername());
        }
    }
}