<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


class Parameter implements ParameterInterface
{
    const FILTER_KEY = 'filters';
    const METRIC_KEY = 'metrics';
    const BREAK_DOWN_KEY = 'breakdowns';

    /** @var FilterParameter */
    protected $filterObject;
    /** @var MetricParameter */
    protected $metricObject;
    /** @var BreakDownParameter */
    protected $breakDownObject;

    function __construct($params)
    {
        if (!array_key_exists(self::FILTER_KEY, $params) || !array_key_exists(self::METRIC_KEY, $params) || !array_key_exists(self::BREAK_DOWN_KEY, $params)) {
            throw new \Exception('Can not find proper parameter to get video report');
        }

        $filterValues = json_decode($params[self::FILTER_KEY], true); // using assoc=true to get as array
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Can not parse filters parameter to get video report');
        }

        $metricValues = json_decode($params[self::METRIC_KEY]);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Can not parse metrics parameter to get video report');
        }

        $breakDownValues = json_decode($params[self::BREAK_DOWN_KEY]);
        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception('Can not parse breakdowns parameter to get video report');
        }

        $this->filterObject = new FilterParameter($filterValues);
        $this->metricObject = new MetricParameter($metricValues);
        $this->breakDownObject = new BreakDownParameter($breakDownValues);
    }

    /**
     * @return BreakDownParameter
     */
    public function getBreakDownObject()
    {
        return $this->breakDownObject;
    }

    /**
     * @return FilterParameter
     */
    public function getFilterObject()
    {
        return $this->filterObject;
    }

    /**
     * @return MetricParameter
     */
    public function getMetricObject()
    {
        return $this->metricObject;
    }
}