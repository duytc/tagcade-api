<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Selectors\Hierarchy;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface SelectorInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param FilterParameterInterface $filterParam
     * @return mixed
     */
    public function getReports(ReportTypeInterface $reportType, FilterParameterInterface $filterParam);

    /**
     * check if selector supports reportType
     * @param ReportTypeInterface $reportType
     * @return bool
     */
    public function supportsReportType(ReportTypeInterface $reportType);
}