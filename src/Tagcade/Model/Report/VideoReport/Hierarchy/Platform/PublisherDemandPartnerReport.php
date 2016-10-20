<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;

class PublisherDemandPartnerReport extends AbstractCalculatedReport implements PublisherDemandPartnerReportInterface
{
    /** @var VideoDemandPartnerInterface */
    protected $videoDemandPartner;

    /** @var VideoPublisherInterface */
    protected $videoPublisher;

    protected $date;
    protected $requests;
    protected $bids;
    protected $bidRate;
    protected $errors;
    protected $errorRate;
    protected $impressions;
    protected $requestFillRate;
    protected $clicks;
    protected $clickThroughRate;
    protected $blocks;

    /**
     * @return float
     */
    protected function calculateFillRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('request must be defined to calculate fill rates');
        }

        return $this->getPercentage($this->getImpressions(), $this->getRequests());
    }

    /**
     * @return float
     */
    protected function calculateBidRate()
    {
        if ($this->getRequests() === null) {
            throw new RuntimeException('requests must be defined to calculate bid rates');
        }

        return $this->getPercentage($this->getBids(), $this->getRequests());
    }

    /**
     * @return float|null
     */
    protected function calculateErrorRate()
    {
        if ($this->getBids() === null) {
            throw new RuntimeException('bids must be defined to calculate error rates');
        }

        return $this->getRatio($this->getErrors(), $this->getBids());
    }

    /**
     * @param ReportInterface $report
     * @return bool
     */
    public function isValidSubReport(ReportInterface $report)
    {
        return true;
    }

    /**
     * @return VideoDemandPartnerInterface
     */
    public function getVideoDemandPartner()
    {
        return $this->videoDemandPartner;
    }

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     */
    public function setVideoDemandPartner($videoDemandPartner)
    {
        $this->videoDemandPartner = $videoDemandPartner;
    }

    /**
     * @return VideoPublisherInterface
     */
    public function getVideoPublisher()
    {
        return $this->videoPublisher;
    }

    /**
     * @param VideoPublisherInterface $videoPublisher
     */
    public function setVideoPublisher($videoPublisher)
    {
        $this->videoPublisher = $videoPublisher;
    }

}