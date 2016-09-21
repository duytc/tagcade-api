<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Result;


use Tagcade\Model\Report\VideoReport\ReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;

interface ReportResultInterface extends \IteratorAggregate
{
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
    public function getStartDate();

    /**
     * @return mixed
     */
    public function getEndDate();
}