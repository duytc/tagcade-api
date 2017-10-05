<?php

namespace Tagcade\Model\Report\PerformanceReport\Display;

use DateTime;

interface ReportInterface extends ReportDataInterface
{
    public function getId();
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
     * @param int $totalOpportunities
     * @return $this
     */
    public function setTotalOpportunities($totalOpportunities);

    /**
     * @param int $impressions
     * @return $this
     */
    public function setImpressions($impressions);

    /**
     * @param int $passbacks
     * @return $this
     */
    public function setPassbacks($passbacks);

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
     * Sets all calculated fields
     * i.e fill rate
     */
    public function setCalculatedFields();

    /**
     * @return $this
     */
    public function setFillRate();

    /**
     * @return int
     */
    public function getAdOpportunities();

    /**
     * @param int $adOpportunities
     * @return self
     */
    public function setAdOpportunities($adOpportunities);

    /**
     * @param int $inBannerRequests
     * @return self
     */
    public function setInBannerRequests($inBannerRequests);

    /**
     * @param int $inBannerImpressions
     * @return self
     */
    public function setInBannerImpressions($inBannerImpressions);

    /**
     * @param int $inBannerTimeouts
     * @return self
     */
    public function setInBannerTimeouts($inBannerTimeouts);
}