<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner;


use Tagcade\Model\Report\PartnerReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportDataInterface;

trait AggregatePartnerReportTrait {

    protected function aggregatePartnerReport(PartnerReportInterface $partnerReport, ReportDataInterface $reportToAdd)
    {
        $partnerReport->setImpressions($partnerReport->getImpressions() + $reportToAdd->getImpressions());
        $partnerReport->setEstRevenue($partnerReport->getEstRevenue() + $reportToAdd->getEstRevenue());

        $partnerReport->setPassbacks($partnerReport->getPassbacks() + $reportToAdd->getPassbacks());
        $partnerReport->setTotalOpportunities($partnerReport->getTotalOpportunities() + $reportToAdd->getTotalOpportunities());
        $partnerReport->setFillRate();
        $partnerReport->calculateEstCpm();
    }


} 