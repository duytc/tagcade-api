<?php

namespace Tagcade\Service\Report\PerformanceReport\Display;


use Tagcade\Exception\LogicException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface as VideoReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\Report\VideoReport\RootReportInterface as VideoRootReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\SubReportInterface;
use Tagcade\Model\Report\VideoReport\SubReportInterface as VideoSubReportInterface;

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
            if (!$current instanceof SubReportInterface) {
                throw new LogicException('Expected SubReportInterface');
            }

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

    /**
     * @param VideoReportInterface $report
     * @return RootReportInterface;
     */
    protected function getVideoRootReport(VideoReportInterface $report)
    {
        if (!$report instanceof VideoSubReportInterface) {
            return $report; // the report is root itself
        }

        $current = $report;

        // Loop 10 times to prevent infinite loop due to programming mistake
        for($i = 0; $i < 10; $i ++) {
            if (!$current instanceof VideoSubReportInterface) {
                throw new LogicException('Expected SubReportInterface');
            }

            $current = $current->getSuperReport();

            if($current instanceof VideoRootReportInterface) {
                break;
            }
        }

        if(!$current instanceof VideoRootReportInterface) {
            throw new LogicException('Expected RootReportInterface');
        }

        return $current;
    }

} 