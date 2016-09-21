<?php

namespace Tagcade\Service\Report\VideoReport\Selector\Grouper\Groupers;

use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Selector\Result\Group\ReportGroup;

interface GrouperInterface
{
    /**
     * @return ReportGroup
     */
    public function getGroupedReport();

    /**
     * @return ReportTypeInterface|ReportTypeInterface[]
     */
    public function getReportType();

    /**
     * @return ReportDataInterface[]
     */
    public function getReports();

    /**
     * @return mixed
     */
    public function getRequests();

    /**
     * @return mixed
     */
    public function getBids();

    /**
     * @return mixed
     */
    public function getBidRate();

    /**
     * @return mixed
     */
    public function getErrors();

    /**
     * @return mixed
     */
    public function getErrorRate();

    /**
     * @return mixed
     */
    public function getImpressions();

    /**
     * @return mixed
     */
    public function getFillRate();

    /**
     * @return mixed
     */
    public function getClicks();

    /**
     * @return mixed
     */
    public function getClickThroughRate();

    /**
     * @return mixed
     */
    public function getAverageRequests();

    /**
     * @return mixed
     */
    public function getAverageBids();

    /**
     * @return mixed
     */
    public function getAverageBidRate();

    /**
     * @return mixed
     */
    public function getAverageErrors();

    /**
     * @return mixed
     */
    public function getAverageErrorRate();

    /**
     * @return mixed
     */
    public function getAverageImpressions();

    /**
     * @return mixed
     */
    public function getAverageFillRate();

    /**
     * @return mixed
     */
    public function getAverageClicks();
}