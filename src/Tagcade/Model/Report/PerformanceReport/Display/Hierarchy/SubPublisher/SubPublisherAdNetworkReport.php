<?php


namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher;

use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PartnerReportFields;
use Tagcade\Model\Report\PerformanceReport\Display\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class SubPublisherAdNetworkReport extends AbstractCalculatedReport implements SubPublisherAdNetworkReportInterface
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

    /** @var AdNetworkInterface */
    protected $adNetwork;
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
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function setAdNetwork(AdNetworkInterface $adNetwork)
    {
        $this->adNetwork = $adNetwork;
        return $this;
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

    public function isValidSubReport(ReportInterface $report)
    {
        return false; // not supported
    }

//    public function getName()
//    {
//        if ($this->subPublisher instanceof SubPublisherInterface) {
//            return $this->subPublisher->getUser()->getUsername();
//        }
//
//        return null;
//    }
}