<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


class MetricParameter implements MetricParameterInterface
{
    const REQUESTS_KEY = 'requests';
    const BID_KEY = 'bids';
    const BID_RATE_KEY = 'bidRate';
    const ERROR_KEY = 'errors';
    const ERROR_RATE_KEY = 'errorRate';
    const IMPRESSION_KEY = 'impressions';
    const REQUEST_FILL_RATE_KEY = 'requestFillRate';
    const CLICK_KEY = 'clicks';
    const CLICK_THROUGH_RATE_KEY = 'clickThroughRate';
    const AD_TAG_ERRORS_KEY = 'adTagErrors';
    const AD_TAG_BIDS_KEY = 'adTagBids';
    const AD_TAG_REQUEST_KEY = 'adTagRequests';

    static $SUPPORTED_METRICS = [
        self::REQUESTS_KEY,
        self::BID_KEY,
        self::BID_RATE_KEY,
        self::ERROR_KEY,
        self::ERROR_RATE_KEY,
        self::IMPRESSION_KEY,
        self::REQUEST_FILL_RATE_KEY,
        self::CLICK_KEY,
        self::CLICK_THROUGH_RATE_KEY,
        self::AD_TAG_ERRORS_KEY,
        self::AD_TAG_BIDS_KEY,
        self::AD_TAG_REQUEST_KEY,
    ];

    protected $usedMetrics = [];

    function __construct(array $metricElements)
    {
        foreach ($metricElements as $metricElement) {
            if (in_array($metricElement, self::$SUPPORTED_METRICS)
                && !in_array($metricElement, $this->usedMetrics)
            ) {
                $this->usedMetrics[] = $metricElement;
            }
        }
    }

    /**
     * @return bool
     */
    public function hasAdTagBids()
    {
        return in_array(self::AD_TAG_BIDS_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */

    public function hasAdTagErrors()
    {
        return in_array(self::AD_TAG_ERRORS_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasAdTagRequest()
    {
        return in_array(self::AD_TAG_REQUEST_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasBidRate()
    {
        return in_array(self::BID_RATE_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasBids()
    {
        return in_array(self::BID_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */

    public function hasClickThroughRate()
    {
        return in_array(self::CLICK_THROUGH_RATE_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasClicks()
    {
        return in_array(self::CLICK_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */

    public function hasError()
    {
        return in_array(self::ERROR_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasErrorRate()
    {
        return in_array(self::ERROR_RATE_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasRequestFillRate()
    {
        return in_array(self::REQUEST_FILL_RATE_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasImpressions()
    {
        return in_array(self::IMPRESSION_KEY, $this->usedMetrics);
    }

    /**
     * @return bool
     */
    public function hasRequests()
    {
        return in_array(self::REQUESTS_KEY, $this->usedMetrics);
    }
}