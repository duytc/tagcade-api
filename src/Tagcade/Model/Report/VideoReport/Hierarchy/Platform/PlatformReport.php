<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\ReportInterface;

class PlatformReport extends AbstractCalculatedReport implements PlatformReportInterface
{
    public function isValidSubReport(ReportInterface $report)
    {
        return $report instanceof AccountReportInterface;
    }

    public function parseData(array $data)
    {
        if (array_key_exists('requests', $data)) {
            $this->setRequests(intval($data['requests']));
        }

        if (array_key_exists('bids', $data)) {
            $this->setBids(intval($data['bids']));
        }

        if (array_key_exists('impressions', $data)) {
            $this->setImpressions(intval($data['impressions']));
        }

        if (array_key_exists('errors', $data)) {
            $this->setErrors(intval($data['errors']));
        }

        if (array_key_exists('clicks', $data)) {
            $this->setClicks(intval($data['clicks']));
        }

        if (array_key_exists('billedAmount', $data)) {
            $this->setBilledAmount(floatval($data['billedAmount']));
        }

        if (array_key_exists('blocks', $data)) {
            $this->setBlocks(intval($data['blocks']));
        }

        if (array_key_exists('adTagBids', $data)) {
            $this->setAdTagBids(intval($data['adTagBids']));
        }

        if (array_key_exists('adTagErrors', $data)) {
            $this->setAdTagErrors(intval($data['adTagErrors']));
        }

        if (array_key_exists('adTagRequests', $data)) {
            $this->setAdTagRequests(intval($data['adTagRequests']));
        }

        return $this;
    }
}