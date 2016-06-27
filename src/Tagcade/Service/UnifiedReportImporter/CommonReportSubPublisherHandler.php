<?php


namespace Tagcade\Service\UnifiedReportImporter;


use Psr\Log\LoggerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\UnifiedReport\CommonReport;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Service\Core\AdTag\PartnerTagIdFinder;
use Tagcade\Service\Core\AdTag\PartnerTagIdFinderInterface;
use Tagcade\Service\Core\Site\SiteServiceInterface;

class CommonReportSubPublisherHandler implements CommonReportSubPublisherHandlerInterface
{
    const AD_TAG_ID_KEY = 'adTagId';
    const SITE_KEY = 'site';
    const IMPRESSIONS_KEY = 'impressions';
    const FILL_RATE_KEY = 'fillRate';
    const PASS_BACK_KEY = 'passbacks';
    const EST_CPM_KEY = 'estCpm';
    const EST_REVENUE_KEY = 'estRevenue';
    const DATE_KEY = 'date';
    const TOTAL_OPPORTUNITIES = 'opportunities';

    /**
     * @var SiteServiceInterface
     */
    protected $siteService;

    /**
     * @var SubPublisherManagerInterface
     */
    protected $subPublisherManager;

    /**
     * @var PartnerTagIdFinderInterface
     */
    protected $partnerTagIdFinder;

    /**
     * @var NetworkDomainAdTagReportRepositoryInterface
     */
    protected $networkDomainAdTagReportRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CommonReportSubPublisherHandler constructor.
     * @param SiteServiceInterface $siteService
     * @param SubPublisherManagerInterface $subPublisherManager
     * @param PartnerTagIdFinderInterface $partnerTagIdFinder
     * @param NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository
     * @param LoggerInterface $logger
     */
    public function __construct(SiteServiceInterface $siteService, SubPublisherManagerInterface $subPublisherManager,
        PartnerTagIdFinderInterface $partnerTagIdFinder, NetworkDomainAdTagReportRepositoryInterface $networkDomainAdTagReportRepository, LoggerInterface $logger)
    {
        $this->siteService = $siteService;
        $this->subPublisherManager = $subPublisherManager;
        $this->partnerTagIdFinder = $partnerTagIdFinder;
        $this->networkDomainAdTagReportRepository = $networkDomainAdTagReportRepository;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function generateCommonReports(AdNetworkInterface $adNetwork, array $rawReports, $override)
    {
        $result = [];

        $today = new \DateTime('today');

        foreach($rawReports as $report) {
            $item = new CommonReport();
            $item->setAdNetwork($adNetwork)
                ->setPublisher($adNetwork->getPublisher())
                ->setAdTagId($report[self::AD_TAG_ID_KEY])
                ->setDate($report[self::DATE_KEY])
                ->setImpressions(intval($report[self::IMPRESSIONS_KEY]))
                ->setFillRate(floatval($report[self::FILL_RATE_KEY]))
                ->setPassbacks(intval($report[self::PASS_BACK_KEY]))
                ->setOpportunities(intval($report[self::TOTAL_OPPORTUNITIES]))
                ->setEstCpm(floatval($report[self::EST_CPM_KEY]))
                ->setEstRevenue(floatval($report[self::EST_REVENUE_KEY]))
                ->setSite(isset($report[self::SITE_KEY]) ? $report[self::SITE_KEY] : null)
            ;

            if ($item->getDate() >= $today) {
                continue;
            }


            // allow item without partner tag id to be processed
            if (is_string($item->getAdTagId())) {
                $adTags = $this->partnerTagIdFinder->getTcTag($adNetwork->getNetworkPartner(), $adNetwork->getPublisher(), $item->getAdTagId());

                // Set site
                if (null === $item->getSite() && array_key_exists(PartnerTagIdFinder::DOMAIN_COUNT_KEY, $adTags) && $adTags[PartnerTagIdFinder::DOMAIN_COUNT_KEY] == 1) {
                    $domain = $adTags[PartnerTagIdFinder::TAGS_KEY][0][PartnerTagIdFinder::DOMAIN_KEY];
                    $item->setSite($domain);
                }
            }

            $subPublishers = $this->siteService->getSubPublisherFromDomain(
                $adNetwork->getNetworkPartner(),
                $adNetwork->getPublisher(),
                $item->getSite()
            );

            if (count($subPublishers) == 1) {
                /** @var SubPublisherInterface $subPublisher */
                $subPublisher = $this->subPublisherManager->find($subPublishers[0][PartnerTagIdFinder::SUB_PUBLISHER_ID_KEY]);

                $item->setSubPublisher($subPublisher);
                $item->setRevenueShareConfigOption($subPublishers[0][PartnerTagIdFinder::REVENUE_CONFIG_KEY][PartnerTagIdFinder::REVENUE_CONFIG_OPTION_KEY]);
                $item->setRevenueShareConfigValue($subPublishers[0][PartnerTagIdFinder::REVENUE_CONFIG_KEY][PartnerTagIdFinder::REVENUE_CONFIG_VALUE_KEY]);
            }

            $result[] = $item;
        }

        if ($override === false) {
            $this->ignoreExistedReports($result);
        }

        return $result;
    }

    protected function ignoreExistedReports(array &$reports)
    {
        /** @var CommonReport $report */
        foreach($reports as $index => $report) {
            if ($this->networkDomainAdTagReportRepository->isRecordExisted($report->getAdNetwork(), $report->getSite(), $report->getAdTagId(), $report->getDate()) === true) {
                unset($reports[$index]);
            }
        }
    }
}