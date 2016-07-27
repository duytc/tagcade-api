<?php


namespace Tagcade\Model\Report\UnifiedReport;


use DateTime;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\ReportModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class CommonReport implements ReportModelInterface
{
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;
    /**
     * @var PublisherInterface
     */
    protected $publisher;
    /**
     * @var string
     */
    protected $adTagId;
    /**
     * @var string
     */
    protected $adTag;
    /**
     * @var string
     */
    protected $creativeSize;
    /**
     * @var string
     */
    protected $site;
    /**
     * @var float
     */
    protected $fillRate;
    /**
     * @var integer
     */
    protected $impressions;
    /**
     * @var integer
     */
    protected $passbacks;
    /**
     * @var integer
     */
    protected $opportunities;
    /**
     * @var float
     */
    protected $estCpm;
    /**
     * @var float
     */
    protected $estRevenue;
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var SubPublisherInterface
     */
    protected $subPublisher;

    protected $revenueShareConfigOption;

    protected $revenueShareConfigValue;

    /**
     * AccountAdTagReport constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return PublisherInterface
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
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @return AdNetworkInterface
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @return self
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdTagId()
    {
        return $this->adTagId;
    }

    /**
     * @param $adTagId
     * @return self
     */
    public function setAdTagId($adTagId)
    {
        $this->adTagId = $adTagId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdTag()
    {
        return $this->adTag;
    }

    /**
     * @param string $adTag
     * @return self
     */
    public function setAdTag($adTag)
    {
        $this->adTag = $adTag;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreativeSize()
    {
        return $this->creativeSize;
    }

    /**
     * @param string $creativeSize
     * @return self
     */
    public function setCreativeSize($creativeSize)
    {
        $this->creativeSize = $creativeSize;
        return $this;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param string $site
     * @return self
     */
    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return float
     */
    public function getFillRate()
    {
        return $this->fillRate;
    }

    /**
     * @param float $fillRate
     * @return self
     */
    public function setFillRate($fillRate)
    {
        $this->fillRate = $fillRate;
        return $this;
    }

    /**
     * @return int
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param int $impressions
     * @return self
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;
        return $this;
    }

    /**
     * @return int
     */
    public function getPassbacks()
    {
        return $this->passbacks;
    }

    /**
     * @param int $passbacks
     * @return self
     */
    public function setPassbacks($passbacks)
    {
        $this->passbacks = $passbacks;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpportunities()
    {
        return $this->opportunities;
    }

    /**
     * @param int $opportunities
     * @return self
     */
    public function setOpportunities($opportunities)
    {
        $this->opportunities = $opportunities;
        return $this;
    }

    /**
     * @return float
     */
    public function getEstCpm()
    {
        return $this->estCpm;
    }

    /**
     * @param float $estCpm
     * @return self
     */
    public function setEstCpm($estCpm)
    {
        $this->estCpm = $estCpm;
        return $this;
    }

    /**
     * @return float
     */
    public function getEstRevenue()
    {
        return $this->estRevenue;
    }

    /**
     * @param float $estRevenue
     * @return self
     */
    public function setEstRevenue($estRevenue)
    {
        $this->estRevenue = $estRevenue;
        return $this;
    }

    /**
     * @return SubPublisherInterface
     */
    public function getSubPublisher()
    {
        return $this->subPublisher;
    }

    /**
     * @param SubPublisherInterface $subPublisher
     * @return self
     */
    public function setSubPublisher($subPublisher)
    {
        $this->subPublisher = $subPublisher;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubPublisherId()
    {
        if ($this->subPublisher instanceof SubPublisherInterface) {
            return $this->subPublisher->getId();
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     * @return self
     */
    public function setDate($date)
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }

        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevenueShareConfigOption()
    {
        return $this->revenueShareConfigOption;
    }

    /**
     * @param mixed $revenueShareConfigOption
     */
    public function setRevenueShareConfigOption($revenueShareConfigOption)
    {
        $this->revenueShareConfigOption = $revenueShareConfigOption;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevenueShareConfigValue()
    {
        return $this->revenueShareConfigValue;
    }

    /**
     * @param mixed $revenueShareConfigValue
     */
    public function setRevenueShareConfigValue($revenueShareConfigValue)
    {
        $this->revenueShareConfigValue = $revenueShareConfigValue;

        return $this;
    }

    public function getCommonReportTagId()
    {
        return sprintf('%s%s%s', $this->adTagId, $this->adTag, $this->creativeSize);
    }
}