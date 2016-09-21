<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


interface BreakDownParameterInterface
{
    /**
     * @return bool
     */
    public function hasPublishers();

    /**
     * @return bool
     */
    public function hasDay();

    /**
     * @return bool
     */
    public function hasVideoDemandAdTags();

    /**
     * @return bool
     */
    public function hasVideoWaterfallTags();

    /**
     * @return bool
     */
    public function hasVideoDemandPartners();

    /**
     * get min breakdown
     *
     * @return string
     */
    public function getMinBreakdown();

    /**
     * @return mixed
     */
    public function hasVideoPublishers();

    /**
     * get min breakdown exclude self::DAY_KEY, because we need determine for grouping reports not by day
     *
     * @return string
     */
    public function getMinBreakdownExcludeDay();
}