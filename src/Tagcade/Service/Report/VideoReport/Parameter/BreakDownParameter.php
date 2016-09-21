<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


class BreakDownParameter implements BreakDownParameterInterface
{
    const DAY_KEY = 'day';
    const PUBLISHER_KEY = 'publisher';
    const VIDEO_PUBLISHER_KEY = 'videoPublisher';
    const VIDEO_DEMAND_PARTNER_KEY = 'demandPartner';
    const VIDEO_WATERFALL_TAG_KEY = 'waterfallTag';
    const VIDEO_DEMAND_AD_TAG_KEY = 'videoDemandAdTag';

    static $SUPPORTED_BREAKDOWNS = [
        self::DAY_KEY,
        self::PUBLISHER_KEY,
        self::VIDEO_PUBLISHER_KEY,
        self::VIDEO_DEMAND_PARTNER_KEY,
        self::VIDEO_WATERFALL_TAG_KEY,
        self::VIDEO_DEMAND_AD_TAG_KEY
    ];

    protected $usedBreakdowns = [];

    function __construct(array $breakDownElements)
    {
        foreach ($breakDownElements as $breakDownElement) {
            if (in_array($breakDownElement, self::$SUPPORTED_BREAKDOWNS)
                && !in_array($breakDownElement, $this->usedBreakdowns)
            ) {
                $this->usedBreakdowns[] = $breakDownElement;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function hasPublishers()
    {
        return in_array(self::PUBLISHER_KEY, $this->usedBreakdowns);
    }

    /**
     * @return mixed
     */
    public function hasDay()
    {
        return in_array(self::DAY_KEY, $this->usedBreakdowns);
    }

    /**
     * @inheritdoc
     */
    public function hasVideoDemandAdTags()
    {
        return in_array(self::VIDEO_DEMAND_AD_TAG_KEY, $this->usedBreakdowns);
    }

    /**
     * @inheritdoc
     */
    public function hasVideoWaterfallTags()
    {
        return in_array(self::VIDEO_WATERFALL_TAG_KEY, $this->usedBreakdowns);
    }

    /**
     * @inheritdoc
     */
    public function hasVideoDemandPartners()
    {
        return in_array(self::VIDEO_DEMAND_PARTNER_KEY, $this->usedBreakdowns);
    }

    /**
     * @inheritdoc
     */
    public function getMinBreakdown()
    {
        // todo: this not beautiful code, need using other way such as priority, ... to get min breakdown
        $minBreakdown = false;

        if ($this->hasDay()) {
            $minBreakdown = self::DAY_KEY;
        }

        if ($this->hasPublishers()) {
            $minBreakdown = self::PUBLISHER_KEY;
        }

        if ($this->hasVideoDemandPartners()) {
            $minBreakdown = self::VIDEO_DEMAND_PARTNER_KEY;
        }

        if ($this->hasVideoPublishers()) {
            $minBreakdown = self::VIDEO_PUBLISHER_KEY;
        }

        if ($this->hasVideoWaterfallTags()) {
            $minBreakdown = self::VIDEO_WATERFALL_TAG_KEY;
        }

        if ($this->hasVideoDemandAdTags()) {
            $minBreakdown = self::VIDEO_DEMAND_AD_TAG_KEY;
        }

        return $minBreakdown;
    }

    /**
     * @inheritdoc
     */
    public function getMinBreakdownExcludeDay()
    {
        $minBreakdown = $this->getMinBreakdown();

        return (false == $minBreakdown || self::DAY_KEY == $minBreakdown) ? false : $minBreakdown;
    }

    /**
     * @return bool
     */
    public function hasVideoPublishers()
    {
        return in_array(self::VIDEO_PUBLISHER_KEY, $this->usedBreakdowns);
    }
}