<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

abstract class AbstractSelector implements SelectorInterface
{
    /**
     * @inheritdoc
     */
    public function getReports(ReportTypeInterface $reportType, FilterParameterInterface $filterParam)
    {
        return $this->doGetReports($reportType, $filterParam);
    }
}