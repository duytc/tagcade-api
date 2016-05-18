<?php

namespace Tagcade\Model\Report;


use Tagcade\Model\ModelInterface;

interface PartnerReportInterface extends ModelInterface{

    public function setImpressions($impressions);

    public function getImpressions();

    public function setEstRevenue($estRevenue);

    public function getEstRevenue();

    public function setPassbacks($passback);

    public function getPassbacks();

    public function setTotalOpportunities($totalOpportunities);

    public function getTotalOpportunities();

    public function setFillRate();

    public function calculateEstCpm();

    public function setEstCpm($estCpm);

    public function getEstCpm();

}