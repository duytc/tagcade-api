<?php


namespace Tagcade\Service\Report\VideoReport\Selector;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface ReportSelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function getReport(ReportTypeInterface $reportType, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

    /**
     * @param ReportTypeInterface $reportType
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function getReportHourly(ReportTypeInterface $reportType, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

    /**
     * @param array $reportTypes
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */

    public function getMultipleReports(array $reportTypes, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

    /**
     * @param array $reportTypes
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function getMultipleReportsHourly(array $reportTypes, FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

} 