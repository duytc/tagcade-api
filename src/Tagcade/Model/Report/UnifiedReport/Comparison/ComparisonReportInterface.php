<?php


namespace Tagcade\Model\Report\UnifiedReport\Comparison;

interface ComparisonReportInterface
{
    /**
     * @return mixed
     */
    public function getRevenueOpportunity();

    public function getTotalOpportunityComparison();

    public function getPassbacksComparison();

    public function getECPMComparison();

    public function getTagcadeECPM();

    /**
     * @return mixed
     */
    public function getPartnerFillRate();
    /**
     * @return mixed
     */
    public function getTagcadeFillRate();
    /**
     * @return mixed
     */
    public function getPartnerTotalOpportunities();
    /**
     * @return mixed
     */
    public function getTagcadeTotalOpportunities();
    /**
     * @return mixed
     */
    public function getPartnerImpressions();
    /**
     * @return mixed
     */
    public function getTagcadeImpressions();
    /**
     * @return mixed
     */
    public function getPartnerPassbacks();
    /**
     * @return mixed
     */
    public function getTagcadePassbacks();
    /**
     * @return mixed
     */
    public function getPartnerEstCPM();
    /**
     * @return mixed
     */
    public function getTagcadeEstCPM();
    /**
     * @return mixed
     */
    public function getPartnerEstRevenue();
    /**
     * @return mixed
     */
    public function getTagcadeEstRevenue();
}