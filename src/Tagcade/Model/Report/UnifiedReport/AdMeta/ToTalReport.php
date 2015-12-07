<?php

namespace Tagcade\Model\Report\UnifiedReport\AdMeta;


use Tagcade\Model\Report\UnifiedReport\UnifiedReportModelInterface;

class ToTalReport implements UnifiedReportModelInterface
{
    protected $id;
    protected $publisherId;
    protected $date;
    protected $website;
    protected $webpage;
    protected $placement;
    protected $orderId;
    protected $orderNumber;
    protected $campaignName;
    protected $orderType;
    protected $clicks;
    protected $impressions;
    protected $placementImpressions;
    protected $actions;
    protected $revenue;
    //revenue-details;
    protected $impressionsRevenue;
    protected $clicksRevenue;
    protected $actionsRevenue;
    //end - revenue-details;
    protected $ctr;
    protected $ecpm;

    function __construct()
    {
        // TODO: Implement __construct() method.
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     * @return self
     */
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActionsRevenue()
    {
        return $this->actionsRevenue;
    }

    /**
     * @param mixed $actionsRevenue
     * @return self
     */
    public function setActionsRevenue($actionsRevenue)
    {
        $this->actionsRevenue = $actionsRevenue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCampaignName()
    {
        return $this->campaignName;
    }

    /**
     * @param mixed $campaignName
     * @return self
     */
    public function setCampaignName($campaignName)
    {
        $this->campaignName = $campaignName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @param mixed $clicks
     * @return self
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClicksRevenue()
    {
        return $this->clicksRevenue;
    }

    /**
     * @param mixed $clicksRevenue
     * @return self
     */
    public function setClicksRevenue($clicksRevenue)
    {
        $this->clicksRevenue = $clicksRevenue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCtr()
    {
        return $this->ctr;
    }

    /**
     * @param mixed $ctr
     * @return self
     */
    public function setCtr($ctr)
    {
        $this->ctr = $ctr;

        return $this;
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
        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEcpm()
    {
        return $this->ecpm;
    }

    /**
     * @param mixed $ecpm
     * @return self
     */
    public function setEcpm($ecpm)
    {
        $this->ecpm = $ecpm;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @param mixed $impressions
     * @return self
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImpressionsRevenue()
    {
        return $this->impressionsRevenue;
    }

    /**
     * @param mixed $impressionsRevenue
     * @return self
     */
    public function setImpressionsRevenue($impressionsRevenue)
    {
        $this->impressionsRevenue = $impressionsRevenue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     * @return self
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param mixed $orderNumber
     * @return self
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getOrderType()
    {
        return $this->orderType;
    }

    /**
     * @param mixed $orderType
     * @return self
     */
    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param mixed $placement
     * @return self
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlacementImpressions()
    {
        return $this->placementImpressions;
    }

    /**
     * @param mixed $placementImpressions
     * @return self
     */
    public function setPlacementImpressions($placementImpressions)
    {
        $this->placementImpressions = $placementImpressions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * @param mixed $publisherId
     * @return self
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRevenue()
    {
        return $this->revenue;
    }

    /**
     * @param mixed $revenue
     * @return self
     */
    public function setRevenue($revenue)
    {
        $this->revenue = $revenue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebpage()
    {
        return $this->webpage;
    }

    /**
     * @param mixed $webpage
     * @return self
     */
    public function setWebpage($webpage)
    {
        $this->webpage = $webpage;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     * @return self
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }
}