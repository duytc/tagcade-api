<?php

namespace Tagcade\Model\Report\HeaderBiddingReport;

use DateTime;

interface ReportInterface extends ReportDataInterface
{
    public function getId();

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return DateTime|null
     */
    public function getDate();

    /**
     * @param DateTime $date
     * @return self
     */
    public function setDate(DateTime $date);

    /**
     * @param int $requests
     * @return self
     */
    public function setRequests($requests);

    /**
     * @param float $billedRate
     * @return self
     */
    public function setBilledRate($billedRate);

    /**
     * @param float $billedAmount
     * @return self
     */
    public function setBilledAmount($billedAmount);

    /**
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields();
}