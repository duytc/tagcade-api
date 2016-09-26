<?php


namespace Tagcade\Model\Report\VideoReport\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\AdTagReportDataInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
interface CalculatedReportInterface extends ReportInterface, AdTagReportDataInterface
{
    /**
     * @param int $adTagRequests
     * @return self
     */
    public function setAdTagRequests($adTagRequests);

    /**
     * @param int $adTagBids
     * @return self
     */
    public function setAdTagBids($adTagBids);

    /**
     * @param int $adTagErrors
     * @return self
     */
    public function setAdTagErrors($adTagErrors);

    /**
     * @param float $billedAmount
     * @return self
     */
    public function setBilledAmount($billedAmount);
}