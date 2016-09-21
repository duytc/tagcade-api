<?php


namespace Tagcade\Service\Report\VideoReport\Grouper;


use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;

interface VideoReportGrouperInterface
{
    /**
     * group Report for all selected reports depends on breakDownParameter
     *
     * @param array $reports
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     */
    public function groupReport(array $reports, BreakDownParameterInterface $breakDownParameter);
}