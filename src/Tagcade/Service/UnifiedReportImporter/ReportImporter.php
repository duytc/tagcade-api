<?php
namespace Tagcade\Service\UnifiedReportImporter;

use Psr\Log\LoggerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
use Tagcade\Service\UnifiedReportImporter\Importer\ImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkAdTagReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkAdTagSubPublisherReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkDomainAdTagReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkSiteReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Network\NetworkSiteSubPublisherReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher\PublisherReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher\SubPublisherNetworkReportImporterInterface;
use Tagcade\Service\UnifiedReportImporter\Importer\UnifiedReport\Publisher\SubPublisherReportImporterInterface;

class ReportImporter implements ReportImporterInterface
{
    /**
     * @var CommonReportSubPublisherHandlerInterface
     */
    protected $commonReportSubPublisherHandler;
    /**
     * @var UnifiedReportGeneratorInterface
     */
    protected $unifiedReportGenerator;
    /**
     * @var ImporterInterface[]
     */
    protected $importers;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ReportImporter constructor.
     * @param CommonReportSubPublisherHandlerInterface $commonReportSubPublisherHandler
     * @param LoggerInterface $logger
     * @param UnifiedReportGeneratorInterface $unifiedReportGenerator
     * @param Importer\ImporterInterface[] $importers
     */
    public function __construct(CommonReportSubPublisherHandlerInterface $commonReportSubPublisherHandler, LoggerInterface $logger,
        UnifiedReportGeneratorInterface $unifiedReportGenerator, array $importers
    )
    {
        $this->commonReportSubPublisherHandler = $commonReportSubPublisherHandler;
        $this->unifiedReportGenerator = $unifiedReportGenerator;
        $this->logger = $logger;
        $this->importers = $importers;
    }


    /**
     * @inheritdoc
     */
    public function importReports(AdNetworkInterface $adNetwork, array $reports, $override)
    {
        $commonReports = $this->commonReportSubPublisherHandler->generateCommonReports($adNetwork, $reports, $override);
        if (count($commonReports) === 0) {
            $this->logger->info(sprintf('%d raw reports given, %d common report generated'), count($reports), count($commonReports));
            return false;
        }

        $adjustedCommonReports = [];
        foreach($this->importers as $importer) {
            if ($importer instanceof NetworkDomainAdTagReportImporterInterface) {
                $networkDomainAdTagReports = $this->unifiedReportGenerator->generateNetworkDomainAdTagReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $adjustedCommonReports = $importer->importReports($networkDomainAdTagReports, $override);
                unset($networkDomainAdTagReports);
                continue;
            }

            if ($importer instanceof NetworkDomainAdTagSubPublisherReportImporterInterface) {
                $networkDomainAdTagSubPublisherReports = $this->unifiedReportGenerator->generateNetworkDomainAdTagForSubPublisherReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkDomainAdTagSubPublisherReports, $override);
                unset($networkDomainAdTagSubPublisherReports);
                continue;
            }
        }

        if ($override === true) {
            if (count($adjustedCommonReports) > 0) {
                $commonReports = $adjustedCommonReports;
                $override = false;
            }
        }

        foreach($this->importers as $importer) {
            if ($importer instanceof NetworkReportImporterInterface) {
                $networkReports = $this->unifiedReportGenerator->generateNetworkReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkReports, $override);
                unset($networkReports);
            }
            elseif ($importer instanceof NetworkSiteReportImporterInterface) {
                $networkSiteReports = $this->unifiedReportGenerator->generateNetworkSiteReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkSiteReports, $override);
                unset($networkSiteReports);
            }
            elseif ($importer instanceof NetworkSiteSubPublisherReportImporterInterface) {
                $networkSiteSubPublisherReports = $this->unifiedReportGenerator->generateNetworkSiteForSubPublisherReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkSiteSubPublisherReports, $override);
                unset($networkSiteReports);
            }
            elseif ($importer instanceof NetworkAdTagSubPublisherReportImporterInterface) {
                $networkAdTagSubPublisherReports = $this->unifiedReportGenerator->generateNetworkAdTagForSubPublisherReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkAdTagSubPublisherReports, $override);
                unset($networkSiteReports);
            }
            elseif ($importer instanceof NetworkAdTagReportImporterInterface) {
                $networkAdTagReports = $this->unifiedReportGenerator->generateNetworkAdTagReports($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($networkAdTagReports, $override);
                unset($networkAdTagReports);
            }
            elseif ($importer instanceof PublisherReportImporterInterface) {
                $publisherReports = $this->unifiedReportGenerator->generatePublisherReport($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($publisherReports, $override);
                unset($publisherReports);
            }
            elseif ($importer instanceof SubPublisherReportImporterInterface) {
                $subPublisherReports = $this->unifiedReportGenerator->generateSubPublisherReport($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($subPublisherReports, $override);
                unset($subPublisherReports);
            } elseif ($importer instanceof SubPublisherNetworkReportImporterInterface) {
                $subPublisherNetworkReports = $this->unifiedReportGenerator->generateSubPublisherNetworkReport($commonReports);
                $this->logger->info(sprintf('start importing unified report for importer %s', get_class($importer)));
                $importer->importReports($subPublisherNetworkReports, $override);
                unset($subPublisherNetworkReports);
            }
        }

        return true;
    }
}