<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


interface ParameterInterface {

    /**
     * @return BreakDownParameter
     */
    public function getBreakDownObject();

    /**
     * @return FilterParameter
     */
    public function getFilterObject();
    /**
     * @return MetricParameter
     */
    public function getMetricObject();

} 