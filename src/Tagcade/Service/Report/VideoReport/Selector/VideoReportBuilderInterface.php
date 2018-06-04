<?php


namespace Tagcade\Service\Report\VideoReport\Selector;


use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoReportBuilderInterface
{
    public function getReports(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter);

    public function getReportsHourly(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter, $force = false);
}