<?php
namespace Tagcade\Service\UnifiedReportImporter;

use Psr\Log\LoggerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteSubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface;

class ReportImporter implements ReportImporterInterface
{
    /** @var CommonReportSubPublisherHandlerInterface */
    protected $commonReportSubPublisherHandler;

    /** @var UnifiedReportGeneratorInterface */
    protected $unifiedReportGenerator;

    /** @var UnifiedReportGrouperInterface */
    protected $unifiedReportGrouper;

    /** @var NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository */
    protected $networkDomainAdTagReportRepository;

    /** @var NetworkDomainAdTagSubPublisherReportRepositoryInterface */
    protected $networkDomainAdTagSubPublisherReportRepository;

    /** @var NetworkSiteReportRepositoryInterface */
    protected $networkSiteReportRepository;

    /** @var NetworkSiteSubPublisherReportRepositoryInterface */
    protected $networkSiteSubPublisherReportRepository;

    /** @var NetworkAdTagReportRepositoryInterface */
    protected $networkAdTagReportRepository;

    /** @var NetworkAdTagSubPublisherReportRepositoryInterface */
    protected $networkAdTagSubPublisherReportRepository;

    /** @var NetworkReportRepositoryInterface */
    protected $networkReportRepository;

    /** @var SubPublisherNetworkReportRepositoryInterface */
    protected $subPublisherNetworkReportRepository;

    /** @var PublisherReportRepositoryInterface */
    protected $publisherReportRepository;

    /** @var SubPublisherReportRepositoryInterface */
    protected $subPublisherReportRepository;

    /** @var LoggerInterface */
    protected $logger;

    protected $batchSize;

    /**
     * ReportImporter constructor.
     * @param CommonReportSubPublisherHandlerInterface $commonReportSubPublisherHandler
     * @param LoggerInterface $logger
     * @param UnifiedReportGeneratorInterface $unifiedReportGenerator
     * @param UnifiedReportGrouperInterface $unifiedReportGrouper
     * @param $batchSize
     * @param NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository
     * @param NetworkDomainAdTagSubPublisherReportRepositoryInterface $networkDomainAdTagSubPublisherReportRepository
     * @param NetworkSiteReportRepositoryInterface $networkSiteReportRepository
     * @param NetworkSiteSubPublisherReportRepositoryInterface $networkSiteSubPublisherReportRepository
     * @param NetworkAdTagReportRepositoryInterface $networkAdTagReportRepository
     * @param NetworkAdTagSubPublisherReportRepositoryInterface $networkAdTagSubPublisherReportRepository
     * @param NetworkReportRepositoryInterface $networkReportRepository
     * @param SubPublisherNetworkReportRepositoryInterface $subPublisherNetworkReportRepository
     * @param PublisherReportRepositoryInterface $publisherReportRepository
     * @param SubPublisherReportRepositoryInterface $subPublisherReportRepository
     */
    public function __construct(CommonReportSubPublisherHandlerInterface $commonReportSubPublisherHandler, LoggerInterface $logger,
        UnifiedReportGeneratorInterface $unifiedReportGenerator, UnifiedReportGrouperInterface $unifiedReportGrouper, $batchSize,
        NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository, NetworkDomainAdTagSubPublisherReportRepositoryInterface $networkDomainAdTagSubPublisherReportRepository,
        NetworkSiteReportRepositoryInterface $networkSiteReportRepository, NetworkSiteSubPublisherReportRepositoryInterface $networkSiteSubPublisherReportRepository,
        NetworkAdTagReportRepositoryInterface $networkAdTagReportRepository, NetworkAdTagSubPublisherReportRepositoryInterface $networkAdTagSubPublisherReportRepository,
        NetworkReportRepositoryInterface $networkReportRepository, SubPublisherNetworkReportRepositoryInterface $subPublisherNetworkReportRepository,
        PublisherReportRepositoryInterface $publisherReportRepository, SubPublisherReportRepositoryInterface $subPublisherReportRepository
    )
    {
        $this->commonReportSubPublisherHandler = $commonReportSubPublisherHandler;
        $this->unifiedReportGenerator = $unifiedReportGenerator;
        $this->unifiedReportGrouper = $unifiedReportGrouper;
        $this->logger = $logger;
        $this->batchSize = $batchSize;
        $this->networkDomainAdTagReportRepository = $networkDomainAdTagReportRepository;
        $this->networkDomainAdTagSubPublisherReportRepository = $networkDomainAdTagSubPublisherReportRepository;
        $this->networkSiteReportRepository = $networkSiteReportRepository;
        $this->networkSiteSubPublisherReportRepository = $networkSiteSubPublisherReportRepository;
        $this->networkAdTagReportRepository = $networkAdTagReportRepository;
        $this->networkAdTagSubPublisherReportRepository = $networkAdTagSubPublisherReportRepository;
        $this->networkReportRepository = $networkReportRepository;
        $this->subPublisherNetworkReportRepository = $subPublisherNetworkReportRepository;
        $this->publisherReportRepository = $publisherReportRepository;
        $this->subPublisherReportRepository = $subPublisherReportRepository;
    }


    /**
     * @inheritdoc
     */
    public function importReports(AdNetworkInterface $adNetwork, array $reports, $override)
    {
        $commonReports = $this->commonReportSubPublisherHandler->generateCommonReports($adNetwork, $reports, $override);
        if (count($commonReports) === 0) {
            $this->logger->info(sprintf('%d raw reports given, %d common report generated', count($reports), count($commonReports)));
            return false;
        }

        $groupedReport = $this->unifiedReportGrouper->groupNetworkDomainAdTagReports($commonReports);
        if (count($groupedReport) > 0) {
            $this->importPublisherBranchReports($groupedReport, $override);
            $this->importSubPublisherBranchReports($groupedReport, $override);

            return true;
        }

        $groupedReport = $this->unifiedReportGrouper->groupNetworkDomainReports($commonReports);
        if (count($groupedReport) > 0) {
            $this->importPublisherBranchReports($groupedReport, $override);
            $this->importSubPublisherBranchReports($groupedReport, $override);

            return true;
        }

        $groupedReport = $this->unifiedReportGrouper->groupNetworkAdTagReports($commonReports);
        if (count($groupedReport) > 0) {
            $this->importPublisherBranchReports($groupedReport, $override);
            $this->importSubPublisherBranchReports($groupedReport, $override);

        }

        $this->importPublisherBranchReports($commonReports, $override);
        $this->importSubPublisherBranchReports($commonReports, $override);
        return true;
    }

    protected function importPublisherBranchReports(array $commonReports, $override)
    {
        $networkDomainAdTagReports = $this->importNetworkDomainAdTagReports($commonReports, $override);

        if (count($networkDomainAdTagReports) > 0) {
            $this->importNetworkDomainReports($commonReports);
            $this->importNetworkAdTagReports($commonReports);
            $this->importAggregatedReports($commonReports);
            return true;
        }

        $networkSiteReports = $this->importNetworkDomainReports($commonReports, $override, true);

        if (count($networkSiteReports) > 0) {
            $this->importAggregatedReports($commonReports);
            return true;
        }

        $networkAdTagReports = $this->importNetworkAdTagReports($commonReports, $override, true);
        if (count($networkAdTagReports) > 0) {
            $this->importAggregatedReports($commonReports);
            return true;
        }

        $networkReports = $this->unifiedReportGenerator->generateNetworkReports($commonReports);
        $commonReports = $this->networkReportRepository->createAdjustedCommonReports($networkReports);

        $this->importAggregatedReports($commonReports);

        return true;
    }

    protected function importSubPublisherBranchReports(array $commonReports, $override)
    {
        $networkDomainAdTagSubPublisherReports = $this->importNetworkDomainAdTagSubPublisherReports($commonReports, $override);

        if (count($networkDomainAdTagSubPublisherReports) > 0) {
            $this->importNetworkDomainSubPublisherReports($commonReports);
            $this->importNetworkAdTagSubPublisherReports($commonReports);
            $this->importAggregatedSubPublisherBranchReports($commonReports);
            return true;
        }

        $networkSiteSubPublisherReports = $this->importNetworkDomainSubPublisherReports($commonReports, $override, $refreshCommonReports = true, $shareRevenue = true);

        if (count($networkSiteSubPublisherReports) > 0) {
            $this->importAggregatedSubPublisherBranchReports($commonReports);
            return true;
        }

        $networkAdTagSubPublisherReports = $this->importNetworkAdTagSubPublisherReports($commonReports, $override, $refreshCommonReports = true, $shareRevenue = true);
        if (count($networkAdTagSubPublisherReports) > 0) {
            $this->importAggregatedSubPublisherBranchReports($commonReports);
            return true;
        }

        $subPublisherNetworkReports = $this->unifiedReportGenerator->generateSubPublisherNetworkReport($commonReports, $shareRevenue = true);
        $commonReports = $this->subPublisherNetworkReportRepository->createAdjustedCommonReports($subPublisherNetworkReports);

        $this->importAggregatedSubPublisherBranchReports($commonReports);

        return true;
    }

    protected function importNetworkDomainAdTagReports(array &$commonReports, $override)
    {
        $networkDomainAdTagReports = $this->unifiedReportGenerator->generateNetworkDomainAdTagReports($commonReports);
        $this->logger->info(sprintf('start importing unified report for NetworkDomainAdTagReport'));
        $adjustedCommonReports = $this->networkDomainAdTagReportRepository->saveMultipleReport($networkDomainAdTagReports, $override);

        if ($override === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkDomainAdTagReports($commonReports, $adjustedCommonReports);
        }

        return $networkDomainAdTagReports;
    }

    protected function importNetworkDomainAdTagSubPublisherReports(array &$commonReports, $override)
    {
        $networkDomainAdTagSubPublisherReports = $this->unifiedReportGenerator->generateNetworkDomainAdTagForSubPublisherReports($commonReports);
        $this->logger->info(sprintf('start importing unified report for NetworkDomainAdTagSubPublisherReport'));
        $adjustedCommonReports = $this->networkDomainAdTagSubPublisherReportRepository->saveMultipleReport($networkDomainAdTagSubPublisherReports, $override);


        if ($override === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkDomainAdTagSubPubReports($commonReports, $adjustedCommonReports);
        }

        return $networkDomainAdTagSubPublisherReports;
    }

    protected function importNetworkDomainReports(array &$commonReports, $override = false, $refreshCommonReports = false)
    {
        $networkSiteReports = $this->unifiedReportGenerator->generateNetworkSiteReports($commonReports);
        $this->logger->info(sprintf('start importing unified report for NetworkSiteReport'));
        $adjustedCommonReports = $this->networkSiteReportRepository->saveMultipleReport($networkSiteReports, $override);

        if ($override === true && $refreshCommonReports === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkDomainReports($commonReports, $adjustedCommonReports);
        }

        return $networkSiteReports;
    }

    protected function importNetworkDomainSubPublisherReports(array &$commonReports, $override = false, $refreshCommonReports = false, $shareRevenue = false)
    {
        $networkSiteSubPublisherReports = $this->unifiedReportGenerator->generateNetworkSiteForSubPublisherReports($commonReports, $shareRevenue);
        $this->logger->info(sprintf('start importing unified report for NetworkSiteSubPublisherReport'));
        $adjustedCommonReports = $this->networkSiteSubPublisherReportRepository->saveMultipleReport($networkSiteSubPublisherReports, $override);


        if ($override === true && $refreshCommonReports === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkDomainSubPubReports($commonReports, $adjustedCommonReports);
        }

        return $networkSiteSubPublisherReports;
    }

    protected function importNetworkAdTagReports(array &$commonReports, $override = false, $refreshCommonReports = false)
    {
        $networkAdTagReports = $this->unifiedReportGenerator->generateNetworkAdTagReports($commonReports);
        $this->logger->info(sprintf('start importing unified report for NetworkAdTagReport'));
        $adjustedCommonReports = $this->networkAdTagReportRepository->saveMultipleReport($networkAdTagReports, $override);

        if ($override === true && $refreshCommonReports === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkAdTagReports($commonReports, $adjustedCommonReports);
        }

        return $networkAdTagReports;
    }

    protected function importNetworkAdTagSubPublisherReports(array &$commonReports, $override = false, $refreshCommonReports = false, $shareRevenue = false)
    {
        $networkAdTagSubPublisherReports = $this->unifiedReportGenerator->generateNetworkAdTagForSubPublisherReports($commonReports, $shareRevenue);
        $this->logger->info(sprintf('start importing unified report for NetworkAdTagSubPublisherReport'));
        $adjustedCommonReports = $this->networkAdTagSubPublisherReportRepository->saveMultipleReport($networkAdTagSubPublisherReports, $override);

        if ($override === true && $refreshCommonReports === true && count($adjustedCommonReports) > 0) {
            $this->mergeNetworkAdTagSubPubReports($commonReports, $adjustedCommonReports);
        }

        return $networkAdTagSubPublisherReports;
    }

    protected function importAggregatedReports(array $commonReports)
    {
        $networkReports = $this->unifiedReportGenerator->generateNetworkReports($commonReports);
        $this->logger->info(sprintf('start importing unified report for NetworkReport'));
        $this->networkReportRepository->saveMultipleReport($networkReports);
        unset($networkReports);

        $publisherReports = $this->unifiedReportGenerator->generatePublisherReport($commonReports);
        $this->logger->info(sprintf('start importing unified report for PublisherReport'));
        $this->publisherReportRepository->saveMultipleReport($publisherReports);
        unset($publisherReports);
    }

    protected function importAggregatedSubPublisherBranchReports(array $commonReports)
    {
        $subPublisherNetworkReports = $this->unifiedReportGenerator->generateSubPublisherNetworkReport($commonReports, $shareRevenue = false);
        $this->logger->info(sprintf('start importing unified report for SubPublisherNetworkReport'));
        $this->subPublisherNetworkReportRepository->saveMultipleReport($subPublisherNetworkReports);
        unset($subPublisherNetworkReports);

        $subPublisherReports = $this->unifiedReportGenerator->generateSubPublisherReport($commonReports);
        $this->logger->info(sprintf('start importing unified report for SubPublisherReport'));
        $this->subPublisherReportRepository->saveMultipleReport($subPublisherReports);
        unset($subPublisherReports);
    }

    protected function mergeNetworkDomainAdTagReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getSite() === $adjustedCommonReport->getSite() &&
                    $commonReport->getAdTagId() === $adjustedCommonReport->getAdTagId() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function mergeNetworkDomainReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getSite() === $adjustedCommonReport->getSite() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function mergeNetworkAdTagReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getAdTagId() === $adjustedCommonReport->getAdTagId() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function mergeNetworkDomainAdTagSubPubReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getSite() === $adjustedCommonReport->getSite() &&
                    $commonReport->getAdTagId() === $adjustedCommonReport->getAdTagId() &&
                    $commonReport->getSubPublisher()->getId() === $adjustedCommonReport->getSubPublisher()->getId() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function mergeNetworkDomainSubPubReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getSite() === $adjustedCommonReport->getSite() &&
                    $commonReport->getSubPublisher()->getId() === $adjustedCommonReport->getSubPublisher()->getId() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function mergeNetworkAdTagSubPubReports(array &$commonReports, array &$adjustedCommonReports)
    {
        /**
         * @var CommonReport $commonReport
         * @var CommonReport $adjustedCommonReport
         */
        foreach($adjustedCommonReports as &$adjustedCommonReport) {
            foreach($commonReports as &$commonReport) {
                if ($commonReport->getAdNetwork()->getId() === $adjustedCommonReport->getAdNetwork()->getId() &&
                    $commonReport->getAdTagId() === $adjustedCommonReport->getAdTagId() &&
                    $commonReport->getSubPublisher()->getId() === $adjustedCommonReport->getSubPublisher()->getId() &&
                    $commonReport->getDate()->format('Y-m-d') === $adjustedCommonReport->getDate()->format('Y-m-d')
                ) {
                    $commonReport = $adjustedCommonReport;
                    break;
                }
            }
        }
    }

    protected function aggregateDuplicatedReports(array &$reports)
    {

    }
}