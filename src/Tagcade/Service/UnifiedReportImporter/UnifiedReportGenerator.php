<?php

namespace Tagcade\Service\UnifiedReportImporter;

use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteReport;
use Tagcade\Entity\Report\UnifiedReport\Network\NetworkSiteSubPublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Publisher\PublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherNetworkReport;
use Tagcade\Entity\Report\UnifiedReport\Publisher\SubPublisherReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class UnifiedReportGenerator implements UnifiedReportGeneratorInterface
{
    use CalculateRevenueTrait;

    protected $totalImpressions = 0;
    protected $totalPassbacks = 0;
    protected $totalOpportunities = 0;
    protected $totalEstCpm = 0;
    protected $totalEstRevenue = 0;

    /**
     * @inheritdoc
     */
    public function generateNetworkReports(array $reports)
    {
        if (empty($reports)) {
            return [];
        }

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = current($reports)->getAdNetwork();

        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][] = $report;
        }

        $networkReports = [];
        foreach($rawReports as $date => $rawReport) {
            $networkReport = new NetworkReport();
            $networkReport
                ->setDate(new \DateTime($date))
                ->setAdNetwork($adNetwork)
                ->setName($adNetwork->getName());
            $this->aggregateReportData($rawReport);
            $this->setReportData($networkReport);

            $networkReports[] = $networkReport;
        }

        return $networkReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkDomainAdTagReports(array $reports)
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

        $networkDomainAdTagReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $domain => $domainReports){
                /** @var CommonReport $adTagReport */
                foreach($domainReports as $adTagId => $adTagReports) {
                    $networkDomainAdTagReport = new NetworkDomainAdTagReport();
                    $networkDomainAdTagReport
                        ->setDate(new \DateTime($date))
                        ->setPartnerTagId($adTagId)
                        ->setAdNetwork($adNetwork)
                        ->setDomain($domain)
                        ->setName($adTagId);

                    $this->aggregateReportData($adTagReports);
                    $this->setReportData($networkDomainAdTagReport);

                    $networkDomainAdTagReports[] = $networkDomainAdTagReport;
                }
            }
        }

        return $networkDomainAdTagReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkDomainAdTagForSubPublisherReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_string($report->getSite()) && is_string($report->getAdTagId()) && $report->getSubPublisherId() != null;
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSubPublisherId()][$report->getSite()][$report->getAdTagId()][] = $report;
        }

        $networkDomainAdTagReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $subPublisherId => $subPublisherReports) {
                foreach($subPublisherReports as $domain => $domainReports){
                    /** @var CommonReport $adTagReport */
                    foreach($domainReports as $adTagId => $adTagReports) {
                        $networkDomainAdTagReport = new NetworkDomainAdTagSubPublisherReport();
                        $networkDomainAdTagReport
                            ->setDate(new \DateTime($date))
                            ->setPartnerTagId($adTagId)
                            ->setAdNetwork($adNetwork)
                            ->setDomain($domain)
                            ->setName($adTagId)
                            ->setSubPublisher($this->aggregateReportData($adTagReports, true))
                        ;

                        $this->setReportData($networkDomainAdTagReport);

                        $networkDomainAdTagReports[] = $networkDomainAdTagReport;
                    }
                }
            }
        }

        return $networkDomainAdTagReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkAdTagReports(array $reports)
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

        $networkAdTagReports = [];
        foreach($rawReports as $date => $rawReport) {
            /** @var CommonReport $adTagReport */
            foreach($rawReport as $adTagId => $adTagReports) {
                $networkAdTagReport = new NetworkAdTagReport();
                $networkAdTagReport
                    ->setDate(new \DateTime($date))
                    ->setPartnerTagId($adTagId)
                    ->setAdNetwork($adNetwork)
                    ->setName($adTagId)
                ;
                $this->aggregateReportData($adTagReports);
                $this->setReportData($networkAdTagReport);

                $networkAdTagReports[] = $networkAdTagReport;
            }
        }

        return $networkAdTagReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkAdTagForSubPublisherReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return $report->getSubPublisherId() != null && is_string($report->getAdTagId());
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSubPublisherId()][$report->getAdTagId()][] = $report;
        }

        $networkAdTagReports = [];
        foreach($rawReports as $date => $subPublisherAdTagReports) {
            foreach($subPublisherAdTagReports as $subPublisherId => $adTagReports) {
                foreach ($adTagReports as $adTagId => $reports) {
                    $networkAdTagReport = new NetworkAdTagSubPublisherReport();
                    $networkAdTagReport
                        ->setPartnerTagId($adTagId)
                        ->setDate(new \DateTime($date))
                        ->setAdNetwork($adNetwork)
                        ->setSubPublisher($this->aggregateReportData($reports, true))
                        ->setName($adTagId)
                    ;
                    $this->setReportData($networkAdTagReport);

                    $networkAdTagReports[] = $networkAdTagReport;
                }
            }
        }

        return $networkAdTagReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkSiteReports(array $reports)
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

        $networkSiteReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $domain => $siteReport) {
                $networkSiteReport = new NetworkSiteReport();
                $networkSiteReport
                    ->setDomain($domain)
                    ->setDate(new \DateTime($date))
                    ->setAdNetwork($adNetwork)
                    ->setName($domain)
                ;
                $this->aggregateReportData($siteReport)
                ;
                $this->setReportData($networkSiteReport);

                $networkSiteReports[] = $networkSiteReport;
            }
        }

        return $networkSiteReports;
    }

    /**
     * @inheritdoc
     */
    public function generateNetworkSiteForSubPublisherReports(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_string($report->getSite()) && $report->getSubPublisherId() != null;
        });

        if (empty($reports)) {
            return [];
        }

        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSubPublisherId()][$report->getSite()][] = $report;
        }

        $networkSiteReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $subPublisherId => $subPublisherSiteReport) {
                foreach ($subPublisherSiteReport as $domain => $siteReport) {
                    $networkSiteReport = new NetworkSiteSubPublisherReport();
                    $networkSiteReport
                        ->setDomain($domain)
                        ->setDate(new \DateTime($date))
                        ->setAdNetwork($adNetwork)
                        ->setSubPublisher($this->aggregateReportData($siteReport, true))
                        ->setName($domain)
                    ;
                    $this->setReportData($networkSiteReport);

                    $networkSiteReports[] = $networkSiteReport;
                }
            }
        }

        return $networkSiteReports;
    }

    /**
     * @inheritdoc
     */
    public function generatePublisherReport(array $reports)
    {
        if (empty($reports)) {
            return [];
        }

        /** @var PublisherInterface $publisher */
        $publisher = current($reports)->getPublisher();

        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][] = $report;
        }

        $publisherReports = [];
        foreach($rawReports as $date => $rawReport) {
            $publisherReport = new PublisherReport();
            $publisherReport
                ->setDate(new \DateTime($date))
                ->setPublisher($publisher)
                ->setName($publisher->getUser()->getUsername())
            ;
            $this->aggregateReportData($rawReport);
            $this->setReportData($publisherReport);

            $publisherReports[] = $publisherReport;
        }

        return $publisherReports;
    }

    /**
     * @param array $reports
     * @return array
     */
    public function generateSubPublisherReport(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_int($report->getSubPublisherId());
        });

        if (empty($reports)) {
            return [];
        }

        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSubPublisherId()][] = $report;
        }

        $subPublisherReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $subPublisherId => $reports){
                $subPublisherReport = new SubPublisherReport();

                $subPublisherReport
                    ->setDate(new \DateTime($date))
                    ->setSubPublisher($this->aggregateReportData($reports, true))
                    ->setName($subPublisherReport->getSubPublisher()->getUser()->getUsername())
                ;
                $this->setReportData($subPublisherReport);

                $subPublisherReports[] = $subPublisherReport;
            }
        }

        return $subPublisherReports;
    }

    public function generateSubPublisherNetworkReport(array $reports)
    {
        $reports = array_filter($reports, function(CommonReport $report) {
            return is_int($report->getSubPublisherId());
        });

        if (empty($reports)) {
            return [];
        }

        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = current($reports)->getAdNetwork();
        $rawReports = [];
        /** @var CommonReport $report */
        foreach($reports as $report) {
            $rawReports[$report->getDate()->format('Y-m-d')][$report->getSubPublisherId()][] = $report;
        }

        $subPublisherNetworkReports = [];
        foreach($rawReports as $date => $rawReport) {
            foreach($rawReport as $subPublisherId => $reports){
                $subPublisherNetworkReport = new SubPublisherNetworkReport();

                $subPublisherNetworkReport
                    ->setDate(new \DateTime($date))
                    ->setAdNetwork($adNetwork)
                    ->setSubPublisher($this->aggregateReportData($reports, true))
                    ->setName($report->getSubPublisher()->getUser()->getUsername())
                ;

                $this->setReportData($subPublisherNetworkReport);

                $subPublisherNetworkReports[] = $subPublisherNetworkReport;
            }
        }

        return $subPublisherNetworkReports;
    }

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

    private function setReportData(ReportInterface $report)
    {
        $report
            ->setImpressions($this->getImpressions())
            ->setTotalOpportunities($this->getOpportunities())
            ->setPassbacks($this->getPassbacks())
            ->setEstRevenue($this->getEstRevenue())
            ->forceSetFillRate($this->getRatio($this->getImpressions(), $this->getOpportunities()))
            ->setEstCpm($this->calculateCpm($this->getEstRevenue(), $this->getImpressions()))
        ;
        $this->clearData();
    }

    private function aggregateReportData(array $reports, $hasSubPublisher = false)
    {
        $subPublisher = null;
        /** @var CommonReport $report */
        foreach($reports as $report) {

            $this->addOpportunities($report->getOpportunities())->addImpressions($report->getImpressions())->addPassbacks($report->getPassbacks());
            if ($hasSubPublisher === true) {
                $this->addEstRevenue($this->calculateRevenue($report->getEstRevenue(), $report->getImpressions(), $report->getRevenueShareConfigOption(), $report->getRevenueShareConfigValue()));
            } else {
                $this->addEstRevenue($report->getEstRevenue());
            }

            if ($subPublisher === null && $hasSubPublisher === true) {
                $subPublisher = $report->getSubPublisher();
            }
        }

        return $subPublisher;
    }
}