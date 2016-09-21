<?php


namespace Tagcade\Service\Report\VideoReport\Grouper;


use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;

class VideoReportGrouper implements VideoReportGrouperInterface
{
    public function groupReport(array $reports, BreakDownParameterInterface $breakDownParameter)
    {
        return $reports;
    }
}