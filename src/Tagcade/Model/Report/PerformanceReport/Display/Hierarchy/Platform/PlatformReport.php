<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    protected function setDefaultName()
    {
        // do nothing, a name isn't needed for this report
    }

    public function parseData(array $data)
    {
        if (array_key_exists('slotOpportunities', $data)) {
            $this->setSlotOpportunities($data['slotOpportunities']);
        }

        if (array_key_exists('totalOpportunities', $data)) {
            $this->setTotalOpportunities($data['totalOpportunities']);
        }

        if (array_key_exists('impressions', $data)) {
            $this->setImpressions($data['impressions']);
        }

        if (array_key_exists('rtbImpressions', $data)) {
            $this->setRtbImpressions($data['rtbImpressions']);
        }

        if (array_key_exists('hbRequests', $data)) {
            $this->setHbRequests($data['hbRequests']);
        }

        if (array_key_exists('passbacks', $data)) {
            $this->setPassbacks($data['passbacks']);
        }

        if (array_key_exists('billedAmount', $data)) {
            $this->setBilledAmount($data['billedAmount']);
        }

        if (array_key_exists('hbBilledAmount', $data)) {
            $this->setHbBilledAmount($data['hbBilledAmount']);
        }

        return $this;
    }
}