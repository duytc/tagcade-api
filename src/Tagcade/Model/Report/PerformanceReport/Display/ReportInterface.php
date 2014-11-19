<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use DateTime;

interface ReportInterface
{
    /**
     * @return string|null
     */
    public function getReportType();

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
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return int|null
     */
    public function getTotalOpportunities();

    /**
     * @param int $totalOpportunities
     * @return $this
     */
    public function setTotalOpportunities($totalOpportunities);

    /**
     * @return int|null
     */
    public function getImpressions();

    /**
     * @param int $impressions
     * @return $this
     */
    public function setImpressions($impressions);

    /**
     * @return int|null
     */
    public function getPassbacks();

    /**
     * @param int $passbacks
     * @return $this
     */
    public function setPassbacks($passbacks);

    /**
     * @return float|null
     */
    public function getFillRate();

    /**
     * @return float|null
     */
    public function getEstRevenue();

    /**
     * @param float $estRevenue
     * @return $this
     */
    public function setEstRevenue($estRevenue);

    /**
     * @param float $estCpm
     * @return $this
     */
    public function setEstCpm($estCpm);

    /**
     * @return float
     */
    public function getEstCpm();

    /**
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields();
}