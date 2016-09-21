<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface TransformerInterface
{
    /**
     * check if supports ReportType And Breakdown
     *
     * @param ReportTypeInterface $reportType
     * @param BreakDownParameterInterface $breakDownParameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter);

    /**
     * @param array $reports
     * @return mixed
     */
    public function transform(array $reports);

}