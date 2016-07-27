<?php


namespace Tagcade\Service\UnifiedReportImporter;


use Tagcade\Model\Report\UnifiedReport\CommonReport;

class UnifiedReportGrouper implements UnifiedReportGrouperInterface
{
    use CalculateRevenueTrait;

    protected $totalImpressions = 0;
    protected $totalPassbacks = 0;
    protected $totalOpportunities = 0;
    protected $totalEstCpm = 0;
    protected $totalEstRevenue = 0;

    protected function addImpressions($impressions)
    {
        if ($impressions !== null) {
            $this->totalImpressions += $impressions;
        }

        return $this;
    }

    protected function addPassbacks($passbacks)
    {
        if ($passbacks !== null) {
            $this->totalPassbacks += $passbacks;
        }

        return $this;
    }

    protected function addOpportunities($opportunities)
    {
        if ($opportunities !== null) {
            $this->totalOpportunities += $opportunities;
        }

        return $this;
    }

    protected function addEstCpm($estCpm)
    {
        if ($estCpm !== null) {
            $this->totalEstCpm += $estCpm;
        }

        return $this;
    }

    protected function addEstRevenue($estRevenue)
    {
        if ($estRevenue !== null) {
            $this->totalEstRevenue += $estRevenue;
        }

        return $this;
    }

    protected function getFillRate()
    {
        return $this->getRatio($this->totalImpressions, $this->totalOpportunities);
    }

    protected function getImpressions()
    {
        return $this->totalImpressions;
    }

    protected function getPassbacks()
    {
        return $this->totalPassbacks;
    }

    protected function getOpportunities()
    {
        return $this->totalOpportunities;
    }

    protected function getEstCpm()
    {
        return $this->totalEstCpm;
    }

    protected function getEstRevenue()
    {
        return $this->totalEstRevenue;
    }

    protected function clearData()
    {
        $this->totalEstRevenue = 0;
        $this->totalEstCpm = 0;
        $this->totalOpportunities = 0;
        $this->totalImpressions = 0;
        $this->totalPassbacks = 0;
    }

    /**
     * @param $numerator
     * @param $denominator
     * @return float|null
     */
    protected function getRatio($numerator, $denominator)
    {
        $ratio = null;

        if (is_numeric($denominator) && $denominator > 0 && is_numeric($numerator)) {
            $ratio = $numerator / $denominator;
        }

        return $ratio;
    }

    private function setReportData(CommonReport $report)
    {
        $report
            ->setImpressions($this->getImpressions())
            ->setOpportunities($this->getOpportunities())
            ->setPassbacks($this->getPassbacks())
            ->setEstRevenue($this->getEstRevenue())
            ->setFillRate($this->getRatio($this->getImpressions(), $this->getOpportunities()))
            ->setEstCpm($this->calculateCpm($this->getEstRevenue(), $this->getImpressions()))
        ;

        $this->clearData();
    }

    private function aggregateReportData(array $reports, $returnSubPublisher = false)
    {
        $subPublisher = null;
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $this->addOpportunities($report->getOpportunities())
                ->addImpressions($report->getImpressions())
                ->addEstRevenue($report->getEstRevenue())
                ->addPassbacks($report->getPassbacks())
            ;

            if ($returnSubPublisher === true && $subPublisher === null) {
                $subPublisher = $report->getSubPublisher();
            }
        }

        return $subPublisher;
    }

    /**
     * @inheritdoc
     */
    public function groupNetworkDomainAdTagReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_string($report->getSite()) && is_string($report->getAdTagId());
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSite()][$report->getAdTagId()][] = $report;
        }

        $commonReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $domain => $domainReports){
                /** @var CommonReport $adTagReport */
                foreach($domainReports as $adTagId => $adTagReports) {
                    $adTagReport = current($adTagReports);
                    if (count($adTagReports) > 1) {
                        $commonReport = new CommonReport();
                        $commonReport
                            ->setAdNetwork($adNetwork)
                            ->setSite($domain)
                            ->setDate(new \DateTime($date))
                            ->setAdTagId($adTagId)
                            ->setSubPublisher($this->aggregateReportData($adTagReports, true))
                            ->setRevenueShareConfigOption($adTagReport->getRevenueShareConfigOption())
                            ->setRevenueShareConfigValue($adTagReport->getRevenueShareConfigValue())
                        ;

                        $this->setReportData($commonReport);
                        $commonReports[] = $commonReport;
                    } else {
                        $commonReports = array_merge($commonReports, $adTagReports);
                    }
                }
            }
        }

        return $commonReports;
    }

    /**
     * @inheritdoc
     */
    public function groupNetworkDomainReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_string($report->getSite());
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSite()][] = $report;
        }

        $commonReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $domain => $domainReports){
                /** @var CommonReport $domainReport */
                $domainReport = current($domainReports);
                if (count($domainReports) > 1) {
                    $commonReport = new CommonReport();
                    $commonReport
                        ->setAdNetwork($adNetwork)
                        ->setDate(new \DateTime($date))
                        ->setSite($domain)
                        ->setSubPublisher($this->aggregateReportData($domainReports, true))
                        ->setRevenueShareConfigOption($domainReport->getRevenueShareConfigOption())
                        ->setRevenueShareConfigValue($domainReport->getRevenueShareConfigValue())
                    ;

                    $this->setReportData($commonReport);
                    $commonReports[] = $commonReport;
                } else {
                    $commonReports = array_merge($commonReports, $domainReports);
                }
            }
        }

        return $commonReports;
    }

    /**
     * @inheritdoc
     */
    public function groupNetworkAdTagReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_string($report->getAdTagId());
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getAdTagId()][] = $report;
        }

        $commonReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $adTagId => $adTagReports) {
                /** @var CommonReport $adTagReport */
                $adTagReport = current($adTagReports);
                if (count($adTagReports) > 1) {
                    $commonReport = new CommonReport();
                    $commonReport
                        ->setAdNetwork($adNetwork)
                        ->setAdTagId($adTagId)
                        ->setDate(new \DateTime($date))
                        ->setSubPublisher($this->aggregateReportData($adTagReports, true))
                        ->setRevenueShareConfigOption($adTagReport->getRevenueShareConfigOption())
                        ->setRevenueShareConfigValue($adTagReport->getRevenueShareConfigValue())
                    ;

                    $this->setReportData($commonReport);
                    $commonReports[] = $commonReport;
                } else {
                    $commonReports = array_merge($commonReports, $adTagReports);
                }
            }
        }

        return $commonReports;
    }
}