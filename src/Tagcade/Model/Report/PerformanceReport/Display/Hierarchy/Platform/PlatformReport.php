<?php

namespace Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform;

use Tagcade\Model\Report\PerformanceReport\CalculateAdOpportunitiesTrait;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    use CalculateAdOpportunitiesTrait;

    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    /**
     * @inheritdoc
     */
    protected function doCalculateFields()
    {
        parent::doCalculateFields();

        // difference calculate at platform level
        $this->setOpportunityFillRate($this->calculateOpportunityFillRate($this->getAdOpportunities(), $this->getSlotOpportunities()));
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

        if (array_key_exists('passbacks', $data)) {
            $this->setPassbacks($data['passbacks']);
        }

        if (array_key_exists('adOpportunities', $data)) {
            $this->setAdOpportunities($data['adOpportunities']);
        }

        if (array_key_exists('opportunityFillRate', $data)) {
            $this->setOpportunityFillRate($data['opportunityFillRate']);
        }

        if (array_key_exists('billedAmount', $data)) {
            $this->setBilledAmount($data['billedAmount']);
        }

        if (array_key_exists('inBannerBilledAmount', $data)) {
            $this->setInBannerBilledAmount($data['inBannerBilledAmount']);
        }

        if (array_key_exists('inBannerBilledRate', $data)) {
            $this->setInBannerBilledRate($data['inBannerBilledRate']);
        }

        if (array_key_exists('inBannerImpressions', $data)) {
            $this->setInBannerImpressions($data['inBannerImpressions']);
        }

        if (array_key_exists('inBannerRequests', $data)) {
            $this->setInBannerRequests($data['inBannerRequests']);
        }

        if (array_key_exists('inBannerTimeouts', $data)) {
            $this->setInBannerTimeouts($data['inBannerTimeouts']);
        }

        return $this;
    }
}