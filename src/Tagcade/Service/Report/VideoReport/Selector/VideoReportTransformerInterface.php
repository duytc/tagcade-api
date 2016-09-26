<?php


namespace Tagcade\Service\Report\VideoReport\Selector;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoReportTransformerInterface
{
    /**
     * transform reports depends on breakDownParameter
     *
     * @param array $reports
     * @param ReportTypeInterface $reportType
     * @param BreakDownParameterInterface $breakDownParameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function transformReport(array $reports, ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter);
}