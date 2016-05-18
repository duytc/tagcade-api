<?php

namespace Tagcade\Model\Report;


trait PartnerReportFields {

    public function calculateEstCpm()
    {
        if ($this->getImpressions() > 0) {
             $this->setEstCpm($this->getEstRevenue() * 1000 / $this->getImpressions());
        }

        return $this;
    }

    protected function calculateFillRate()
    {
        return $this->getTotalOpportunities() > 0 ? $this->getImpressions() / $this->getTotalOpportunities() : 0;
    }

    public abstract function getImpressions();

    public abstract function getTotalOpportunities();

    public abstract function getEstRevenue();

    public abstract function setEstCpm($estCpm);
}