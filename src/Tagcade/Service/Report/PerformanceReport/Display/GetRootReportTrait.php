<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;


use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;

trait GetRootReportTrait {

    /**
     * @param ReportInterface $report
     * @return RootReportInterface;
     */
    protected function getRootReport(ReportInterface $report)
    {
        if (!$report instanceof SubReportInterface) {
            return $report; // the report is root itself
        }

        $current = $report;

        // Loop 10 times to prevent infinite loop due to programming mistake
        for($i = 0; $i < 10; $i ++) {
            $current = $current->getSuperReport();
            if($current instanceof RootReportInterface) {
                break;
            }
        }

        if(!$current instanceof RootReportInterface) {
            throw new LogicException('Expected RootReportInterface');
        }

        return $current;
    }

} 