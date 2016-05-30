<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Psr\Log\LoggerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Partner\AccountReport as PartnerAccountReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkAdTagReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkDomainAdTagReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkDomainAdTagSubPublisherReport;
use Tagcade\Entity\Report\PerformanceReport\Display\Partner\AdNetworkDomainReport;
use Tagcade\Entity\Report\PerformanceReport\Display\SubPublisher\SubPublisherAdNetworkReport;
use Tagcade\Entity\Report\PerformanceReport\Display\SubPublisher\SubPublisherReport;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdTagReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\SiteReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Partner\AggregatePartnerReportTrait;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportRepositoryInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Selectors\SelectorInterface;

class HistoryReportCreator
{
    use AggregatePartnerReportTrait;

    /** @var DateTime */
    private $reportDate;

    /** @var ObjectManager */
    private $om;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var SelectorInterface[] */
    protected $selectors;

    /** @var AdNetworkManagerInterface */
    private $adNetworkManager;

    /** @var SiteManagerInterface */
    private $siteManager;

    /** @var AdTagManagerInterface */
    private $adTagManager;

    /** @var AccountReportRepositoryInterface */
    private $accountReportRepository;

    /** @var AdNetworkAdTagReportRepositoryInterface */
    private $adNetworkAdTagReportRepository;

    /** @var AdNetworkDomainAdTagReportRepositoryInterface */
    private $adNetworkDomainAdTagReportRepository;

    /** @var AdNetworkDomainReportRepositoryInterface */
    private $adNetworkDomainReportRepository;

    /** @var AdNetworkDomainAdTagSubPublisherReportRepositoryInterface */
    private $adNetworkDomainAdTagSubPublisherReportRepository;

    /** @var  SubPublisherAdNetworkReportRepositoryInterface */
    private $subPublisherAdNetworkReportRepository;

    /** @var SubPublisherReportRepositoryInterface */
    private $subPublisherReportRepository;

    /**
     * HistoryReportCreator constructor.
     * @param ObjectManager $om
     * @param array $selectors
     * @param LoggerInterface $logger
     * @param AdNetworkManagerInterface $adNetworkManager
     * @param SiteManagerInterface $siteManager
     * @param AdTagManagerInterface $adTagManager
     * @param AccountReportRepositoryInterface $accountReportRepository
     * @param AdNetworkAdTagReportRepositoryInterface $adNetworkAdTagReportRepository
     * @param AdNetworkDomainAdTagReportRepositoryInterface $adNetworkDomainAdTagReportRepository
     * @param AdNetworkDomainReportRepositoryInterface $adNetworkDomainReportRepository
     * @param AdNetworkDomainAdTagSubPublisherReportRepositoryInterface $adNetworkDomainAdTagSubPublisherReportRepository
     * @param SubPublisherAdNetworkReportRepositoryInterface $subPublisherAdNetworkReportRepository
     * @param SubPublisherReportRepositoryInterface $subPublisherReportRepository
     */
    public function __construct(ObjectManager $om, array $selectors, LoggerInterface $logger,
                                AdNetworkManagerInterface $adNetworkManager, SiteManagerInterface $siteManager, AdTagManagerInterface $adTagManager,
                                AccountReportRepositoryInterface $accountReportRepository, AdNetworkAdTagReportRepositoryInterface $adNetworkAdTagReportRepository,
                                AdNetworkDomainAdTagReportRepositoryInterface $adNetworkDomainAdTagReportRepository, AdNetworkDomainReportRepositoryInterface $adNetworkDomainReportRepository,
                                AdNetworkDomainAdTagSubPublisherReportRepositoryInterface $adNetworkDomainAdTagSubPublisherReportRepository, SubPublisherAdNetworkReportRepositoryInterface $subPublisherAdNetworkReportRepository,
                                SubPublisherReportRepositoryInterface $subPublisherReportRepository
    )
    {
        $this->om = $om;
        $this->logger = $logger;

        foreach ($selectors as $selector) {
            if (!$selector instanceof SelectorInterface) {
                continue;
            }

            $this->selectors = $selectors;
        }

        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adTagManager = $adTagManager;

        $this->accountReportRepository = $accountReportRepository;
        $this->adNetworkAdTagReportRepository = $adNetworkAdTagReportRepository;
        $this->adNetworkDomainReportRepository = $adNetworkDomainReportRepository;
        $this->adNetworkDomainAdTagReportRepository = $adNetworkDomainAdTagReportRepository;
        $this->adNetworkDomainAdTagSubPublisherReportRepository = $adNetworkDomainAdTagSubPublisherReportRepository;
        $this->subPublisherAdNetworkReportRepository = $subPublisherAdNetworkReportRepository;
        $this->subPublisherReportRepository = $subPublisherReportRepository;
    }

    /**
     * @return DateTime
     */
    public function getReportDate()
    {
        return $this->reportDate;
    }

    /**
     * @param DateTime $reportDate
     * @return $this
     */
    public function setReportDate(DateTime $reportDate)
    {
        $this->reportDate = $reportDate;

        return $this;
    }

    /**
     * Create all performance reports for partners and persist them
     *
     * @param PublisherInterface $publisher
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    public function createAndSaveForSinglePublisher(PublisherInterface $publisher, $override = false)
    {
        $adNetworks = $this->adNetworkManager->getAdNetworksForPublisher($publisher);

        $allAdNetworkReports = [];
        $allSubPublisherNetworkReports = [];

        /** @var Params $params */
        $params = new Params($this->reportDate, $this->reportDate, $expanded = false, $grouped = false);

        try {
            foreach ($adNetworks as $adNetwork) {
                // sure adNetwork has a partner
                if (!$adNetwork->getNetworkPartner() instanceof AdNetworkPartnerInterface) {
                    continue;
                }

                $partnerReports = $this->getPartnerReports($adNetwork, $params);

                if (!is_array($partnerReports)) {
                    continue;
                }

                // get reports for network and add to account reports of publisher
                $partnerNetworkReports = $this->getPartnerNetworkReports($partnerReports);
                if (!is_array($partnerNetworkReports)) {
                    continue;
                }

                $allAdNetworkReports = array_merge($allAdNetworkReports, $partnerNetworkReports);

                // create Domain And AdTag Reports For Partner
                $this->createDomainAndAdTagReportsForPartner($partnerReports, $override);
                $this->logger->info('finish saving domain ad tag reports');

                // create Domain And AdTag SubPublisher Reports For Partner
                $this->createDomainAdTagSubPublisherReportsForPartner($partnerReports, $override);
                $this->logger->info('finish saving domain ad tag sub publisher reports');

                // create SubPublisher Reports For Ad Network
                $subPublisherNetworkReports = $this->getSubPublisherNetworkReports($partnerReports);

                if (!is_array($subPublisherNetworkReports)) {
                    continue;
                }

                $allSubPublisherNetworkReports = array_merge($allSubPublisherNetworkReports, $subPublisherNetworkReports);

                $this->createSubPublisherNetworkReport($subPublisherNetworkReports, $override);

                unset(
                    $partnerReports,
                    $partnerNetworkReports,
                    $subPublisherNetworkReports
                );
            }

            // create SubPublisher Reports
            $this->createSubPublisherReport($allSubPublisherNetworkReports, $override);
            $this->flushThenDetach($allSubPublisherNetworkReports);
            unset($allSubPublisherNetworkReports);

            // create Account Reports For Partner
            $this->createAccountReportsForPartner($allAdNetworkReports, $override);
            $this->om->flush();
            //        $this->flushThenDetach($allAdNetworkReports);
            unset($allAdNetworkReports);

            gc_collect_cycles();
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    public function createAndSave(array $publishers, $override = false)
    {
        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }

            try {
                $this->createAndSaveForSinglePublisher($publisher, $override);
            } catch (UniqueConstraintViolationException $ex) {
                throw $ex;
            }
        }
    }

    /**
     * flush Then Detach
     *
     * @param $entities
     */
    protected function flushThenDetach($entities)
    {
        $this->om->flush();

        $this->detach($entities);
    }

    protected function detach($entities)
    {
        $myEntities = is_array($entities) ? $entities : [$entities];

        foreach ($myEntities as $entity) {
            $tmp = is_array($entity) ? $entity : [$entity];

            foreach ($tmp as $e) {
                $this->om->detach($e);
            }
        }
    }

    /**
     * get Partner Reports, includes network, network-site, network-ad tag, network-site-ad tag
     *
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return array [ site => [], siteAdTag => [], adTag => [], subPublisherNetwork => [] ]
     */
    protected function getPartnerReports(AdNetworkInterface $adNetwork, Params $params)
    {
        $partnerReport = [
            'site' => [],
            'siteAdTag' => [],
            'adTag' => [],
            'subPublisherNetwork' => [],
            'subPublisherNetworkDomainAdTag' => []
        ];

        $networkSubPublisherReports = [];

        // get report for ad network
        $partnerReport['network'] = $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);

        // get reports for each site related to ad network
        /** @var SiteInterface[] $sites */
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);
        $partnerReport['site'] = [];

        foreach ($sites as $site) {
            /** @var SiteReport[] $siteReports */
            $siteReports = $this->getReports(new AdNetworkReportTypes\Site($site, $adNetwork), $params);

            $partnerDomainReports = [];

            foreach ($siteReports as $siteReport) {
                $r = new AdNetworkDomainReport();
                $r
                    ->setDomain($site->getDomain())
                    ->setAdNetwork($adNetwork)
                    ->setDate($siteReport->getDate())
                    ->setName($site->getDomain());
                $this->aggregatePartnerReport($r, $siteReport);

                $partnerDomainReports[] = $r;

                // also, create sub publisher network report
                /** @var SubPublisherInterface $subPublisher */
                $subPublisher = $site->getSubPublisher();

                if (!$subPublisher instanceof SubPublisherInterface) {
                    continue;
                }

                $subPublisherNetworkKey = $subPublisher->getId() . '-' . $adNetwork->getId();

                if (!array_key_exists($subPublisherNetworkKey, $networkSubPublisherReports)) {
                    $r = new SubPublisherAdNetworkReport();
                    $r
                        ->setAdNetwork($adNetwork)
                        ->setSubPublisher($site->getSubPublisher())
                        ->setDate($siteReport->getDate())
                        ->setName($adNetwork->getName());

                    $networkSubPublisherReports[$subPublisherNetworkKey] = $r;
                }

                // do summary
                $this->aggregatePartnerReport($networkSubPublisherReports[$subPublisherNetworkKey], $siteReport);
            }

            // check if report for domain already existed in $partnerReport['site'] (because many tags may have same partnerTagId!!!)
            // so, we must do summary by domain
            if (array_key_exists($site->getDomain(), $partnerReport['site'])) {
                $existedPartnerDomainReports = $partnerReport['site'][$site->getDomain()];

                if (count($partnerDomainReports) > 0 && count($existedPartnerDomainReports) > 0) {
                    $this->aggregatePartnerReport($partnerDomainReports[0], $existedPartnerDomainReports[0]); // we have only one element in array $partnerDomainReports
                }
            }

            $partnerReport['site'][$site->getDomain()] = $partnerDomainReports;

            // get reports for each ad tag related to site
            /** @var AdTagInterface[] $adTags */
            $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

            foreach ($adTags as $adTag) {
                // sure partnerTagId not null for mapping tagcade-partner report
                $partnerTagId = $adTag->getPartnerTagId();

                if (empty($partnerTagId)) {
                    continue;
                }

                /** @var AdTagReport[] $adTagReports */
                $adTagReports = $this->getReports(new PlatformReportTypes\AdTag($adTag), $params);

                $partnerDomainAdTagReports = [];
                $partnerDomainAdTagSubPublisherReports = [];

                foreach ($adTagReports as $adTagReport) {
                    $r = new AdNetworkDomainAdTagReport();
                    $r
                        ->setPartnerTagId($partnerTagId)
                        ->setAdNetwork($adNetwork)
                        ->setDomain($site->getDomain())
                        ->setDate($adTagReport->getDate())
                        ->setName($adTagReport->getAdTag()->getName());
                    $this->aggregatePartnerReport($r, $adTagReport);

                    $partnerDomainAdTagReports[] = $r;

                    // also partnerDomainAdTagSubPublisher report
                    /** @var SubPublisherInterface $subPublisher */
                    $subPublisher = $site->getSubPublisher();

                    if ($subPublisher instanceof SubPublisherInterface) {
                        $r1 = new AdNetworkDomainAdTagSubPublisherReport();
                        $r1
                            ->setPartnerTagId($partnerTagId)
                            ->setAdNetwork($adNetwork)
                            ->setDomain($site->getDomain())
                            ->setSubPublisher($subPublisher)
                            ->setDate($adTagReport->getDate())
                            ->setName($adTagReport->getAdTag()->getName());
                        $this->aggregatePartnerReport($r1, $adTagReport);

                        $partnerDomainAdTagSubPublisherReports[] = $r1;
                    }
                }

                $domainTagKey = $site->getDomain() . '-' . $partnerTagId;
                // check if report for domain-partnerTagId already existed in $partnerReport['siteAdTag'] (because many tags may have same partnerTagId!!!)
                // so, we must do summary by domain-partnerTagId
                if (array_key_exists($domainTagKey, $partnerReport['siteAdTag'])) {
                    $existedPartnerDomainAdTagReports = $partnerReport['siteAdTag'][$domainTagKey];

                    if (count($partnerDomainAdTagReports) > 0 && count($existedPartnerDomainAdTagReports) > 0) {
                        $this->aggregatePartnerReport($partnerDomainAdTagReports[0], $existedPartnerDomainAdTagReports[0]); // we have only one element in array $partnerDomainAdTagReports
                    }
                }

                $partnerReport['siteAdTag'][$domainTagKey] = $partnerDomainAdTagReports;

                // also create networkDomainAdTagSubPublisher report
                /** @var SubPublisherInterface $subPublisher */
                $subPublisher = $site->getSubPublisher();

                if (!$subPublisher instanceof SubPublisherInterface) {
                    continue;
                }

                $domainTagSubPublisherKey = $site->getDomain() . '-' . $partnerTagId . '-' . $subPublisher->getId();

                // check if report for domain-partnerTagId-subPublisher already existed in $partnerReport['subPublisherNetworkDomainAdTag'] (because many tags may have same partnerTagId, domain and subPublisher!!!)
                // so, we must do summary by domain-partnerTagId
                if (array_key_exists($domainTagSubPublisherKey, $partnerReport['subPublisherNetworkDomainAdTag'])) {
                    $existedPartnerDomainAdTagSubPublisherReports = $partnerReport['subPublisherNetworkDomainAdTag'][$domainTagSubPublisherKey];

                    if (count($partnerDomainAdTagSubPublisherReports) > 0 && count($existedPartnerDomainAdTagSubPublisherReports) > 0) {
                        $this->aggregatePartnerReport($partnerDomainAdTagSubPublisherReports[0], $existedPartnerDomainAdTagSubPublisherReports[0]); // we have only one element in array $partnerDomainAdTagSubPublisherReports
                    }
                }

                $partnerReport['subPublisherNetworkDomainAdTag'][$domainTagSubPublisherKey] = $partnerDomainAdTagSubPublisherReports;
            }

            unset ($siteReports, $adTagReports);
        }

        // end sites loop, set networkSubPublisher reports
        $partnerReport['subPublisherNetwork'] = $networkSubPublisherReports;

        // get reports for each ad tag related to ad network
        /** @var AdTagInterface[] $adTags */
        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        foreach ($adTags as $adTag) {
            // sure partnerTagId not null for mapping tagcade-partner report
            $partnerTagId = $adTag->getPartnerTagId();

            if (empty($partnerTagId)) {
                continue;
            }

            /** @var AdTagReport[] $adTagReports */
            $adTagReports = $this->getReports(new PlatformReportTypes\AdTag($adTag), $params);

            $partnerAdTagReports = [];

            foreach ($adTagReports as $adTagReport) {
                $r = new AdNetworkAdTagReport();
                $r
                    ->setPartnerTagId($partnerTagId)
                    ->setAdNetwork($adNetwork)
                    ->setDate($adTagReport->getDate())
                    ->setName($adTagReport->getAdTag()->getName());
                $this->aggregatePartnerReport($r, $adTagReport);

                $partnerAdTagReports[] = $r;
            }

            // check if report for partnerTagId already existed in $partnerReport['adTag'] (because many tags may have same partnerTagId!!!)
            // so, we must do summary by partnerTagId
            if (array_key_exists($partnerTagId, $partnerReport['adTag'])) {
                $existedPartnerAdTagReports = $partnerReport['adTag'][$partnerTagId];

                if (count($partnerAdTagReports) > 0 && count($existedPartnerAdTagReports) > 0) {
                    $this->aggregatePartnerReport($partnerAdTagReports[0], $existedPartnerAdTagReports[0]); // we have only one element in array $partnerAdTagReports
                }
            }

            // using partnerTagId as key to sure not duplicate!!!
            $partnerReport['adTag'][$partnerTagId] = $partnerAdTagReports;
        }

        return $partnerReport;
    }

    /**
     * create Domain And AdTag Reports For Partner
     *
     * @param $partnerReports
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    protected function createDomainAndAdTagReportsForPartner($partnerReports, $override = false)
    {
        // get and save reports for domains
        $partnerDomainReports = $this->getPartnerDomainReports($partnerReports);

        if (!is_array($partnerDomainReports)) {
            return;
        }

        try {
            foreach ($partnerDomainReports as $partnerDomainReport) {
                foreach ($partnerDomainReport as $partnerDomain) {
                    $override === false ? $this->om->persist($partnerDomain) : $this->adNetworkDomainReportRepository->override($partnerDomain);
                }
            }

            // get and save reports for ad tags
            $partnerAdTagReports = $this->getPartnerAdTagReports($partnerReports);

            if (!is_array($partnerAdTagReports)) {
                return;
            }

            foreach ($partnerAdTagReports as $partnerAdTagReport) {
                foreach ($partnerAdTagReport as $partnerAdTag) {
                    $override === false ? $this->om->persist($partnerAdTag) : $this->adNetworkAdTagReportRepository->override($partnerAdTag);
                }
            }

            // get and save reports for ad tags
            $partnerDomainAdTagReports = $this->getPartnerDomainAdTagReports($partnerReports);

            if (!is_array($partnerDomainAdTagReports)) {
                return;
            }

            foreach ($partnerDomainAdTagReports as $partnerDomainAdTagReport) {
                foreach ($partnerDomainAdTagReport as $partnerDomainAdTag) {
                    $override === false ? $this->om->persist($partnerDomainAdTag) : $this->adNetworkDomainAdTagReportRepository->override($partnerDomainAdTag);
                }
            }

            if ($override === false) {
                $this->om->flush();
            }

            $entitiesToFlushAndDetach = array_merge($partnerDomainReports, $partnerAdTagReports, $partnerDomainAdTagReports); // do not add $partnerNetworkReports to detach

            foreach ($entitiesToFlushAndDetach as $key => $entity) {
                foreach ($entity as $report)
                    $this->om->detach($report);
            }
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    /**
     * create Domain-AdTag-SubPublisher Reports For Partner
     *
     * @param $partnerReports
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    protected function createDomainAdTagSubPublisherReportsForPartner($partnerReports, $override = false)
    {
        // get and save reports for domain-adTag-SubPublisher
        $partnerDomainTagSubPublisherReports = $this->getPartnerDomainAdTagSubPublisherReports($partnerReports);

        if (!is_array($partnerDomainTagSubPublisherReports)) {
            return;
        }

        try {
            foreach ($partnerDomainTagSubPublisherReports as $partnerDomainAdTagSubPublisherReport) {
                foreach ($partnerDomainAdTagSubPublisherReport as $partnerDomainAdTagSubPublisher) {
                    $override === false ? $this->om->persist($partnerDomainAdTagSubPublisher) : $this->adNetworkDomainAdTagSubPublisherReportRepository->override($partnerDomainAdTagSubPublisher);
                }
            }

            $this->flushThenDetach($partnerDomainTagSubPublisherReports);
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    /**
     * create SubPublisher Network Report due to subPublisherNetworkReports
     *
     * @param array $subPublisherNetworkReports
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    protected function createSubPublisherNetworkReport(array $subPublisherNetworkReports, $override = false)
    {
        try {
            foreach ($subPublisherNetworkReports as $subPublisherNetworkReport) {
                /** @var SubPublisherAdNetworkReportInterface $subPublisherNetworkReport */
                $r = new SubPublisherAdNetworkReport();
                $r
                    ->setAdNetwork($subPublisherNetworkReport->getAdNetwork())
                    ->setSubPublisher($subPublisherNetworkReport->getSubPublisher())
                    ->setDate($subPublisherNetworkReport->getDate())
                    ->setName($subPublisherNetworkReport->getAdNetwork()->getName());

                $this->aggregatePartnerReport($r, $subPublisherNetworkReport);

                $override === false ? $this->om->persist($r) : $this->subPublisherAdNetworkReportRepository->override($r);
            }

            $this->flushThenDetach($subPublisherNetworkReports);
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    /**
     * create SubPublisher Report due to allSubPublisherNetworkReports
     *
     * @param array $allSubPublisherNetworkReports
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    protected function createSubPublisherReport(array $allSubPublisherNetworkReports, $override = false)
    {
        $subPublisherReports = [];

        try {
            foreach ($allSubPublisherNetworkReports as $subPublisherNetworkReport) {
                /** @var SubPublisherAdNetworkReportInterface $subPublisherNetworkReport */
                $subPublisherId = $subPublisherNetworkReport->getSubPublisher()->getId();

                if (!array_key_exists($subPublisherId, $subPublisherReports)) {
                    $r = new SubPublisherReport();
                    $r
                        ->setDate($subPublisherNetworkReport->getDate())
                        ->setName($subPublisherNetworkReport->getSubPublisher()->getUser()->getUsername())
                        ->setSubPublisher($subPublisherNetworkReport->getSubPublisher());

                    $subPublisherReports[$subPublisherId] = $r;
                }

                // do the summary
                $this->aggregatePartnerReport($subPublisherReports[$subPublisherId], $subPublisherNetworkReport);

                $override === false ? $this->om->persist($subPublisherReports[$subPublisherId]) : $this->subPublisherReportRepository->override($subPublisherReports[$subPublisherId]);
            }

            $this->flushThenDetach($subPublisherReports);
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    /**
     * create Account Reports For Partner due to all AdNetwork Reports
     *
     * @param $allAdNetworkReports
     * @param bool $override
     * @throws Exception
     * @throws UniqueConstraintViolationException
     */
    protected function createAccountReportsForPartner($allAdNetworkReports, $override = false)
    {
        if (!is_array($allAdNetworkReports)) {
            return;
        }

        // get and save account reports
        $partnerAccountReports = $this->getPartnerAccountReport($allAdNetworkReports);

        if (!is_array($partnerAccountReports)) {
            return;
        }

        try {
            foreach ($partnerAccountReports as $accountReport) {
                $override === false ? $this->om->persist($accountReport) : $this->accountReportRepository->override($accountReport);
            }
        } catch (UniqueConstraintViolationException $ex) {
            throw $ex;
        }
    }

    /**
     * get network report
     *
     * @param array $networkReport
     * @return bool|Object
     */
    protected function getPartnerNetworkReports(array $networkReport)
    {
        return array_key_exists('network', $networkReport) ? $networkReport['network'] : false;
    }

    /**
     * get network-domain-ad tag report
     *
     * @param array $networkReport
     * @return bool|array
     */
    protected function getPartnerDomainAdTagReports(array $networkReport)
    {
        return array_key_exists('siteAdTag', $networkReport) ? $networkReport['siteAdTag'] : false;
    }

    /**
     * get network-domain report
     *
     * @param array $networkReport
     * @return bool|array
     */
    protected function getPartnerDomainReports(array $networkReport)
    {
        return array_key_exists('site', $networkReport) ? $networkReport['site'] : false;
    }

    /**
     * get network-ad tag report
     *
     * @param array $networkReport
     * @return bool|array
     */
    protected function getPartnerAdTagReports(array $networkReport)
    {
        return array_key_exists('adTag', $networkReport) ? $networkReport['adTag'] : false;
    }

    /**
     * get network-domain-adTag-subPublisher report
     *
     * @param array $networkReport
     * @return bool|array
     */
    protected function getPartnerDomainAdTagSubPublisherReports(array $networkReport)
    {
        return array_key_exists('subPublisherNetworkDomainAdTag', $networkReport) ? $networkReport['subPublisherNetworkDomainAdTag'] : false;
    }

    /**
     * get subPublisher network report
     *
     * @param array $networkReport
     * @return bool|array
     */
    protected function getSubPublisherNetworkReports(array $networkReport)
    {
        return array_key_exists('subPublisherNetwork', $networkReport) ? $networkReport['subPublisherNetwork'] : false;
    }

    /**
     * get Partner Account Report
     * @param array $adNetworkReports
     * @return array
     */
    protected function getPartnerAccountReport(array $adNetworkReports)
    {
        $partnerAccountReports = [];

        // creating account partner report from generated data
        foreach ($adNetworkReports as $adNetworkReport) {
            /** @var AdNetworkReport $adNetworkReport */
            $adNetwork = $adNetworkReport->getAdNetwork();

            /** @var AdNetworkInterface $adNetwork */
            if (!array_key_exists($adNetwork->getPublisherId(), $partnerAccountReports)) {
                $partnerAccountReport = new PartnerAccountReport();
                $partnerAccountReport->setDate($adNetworkReport->getDate());
                $partnerAccountReport->setPublisher($adNetwork->getPublisher());
                $partnerAccountReports[$adNetwork->getPublisherId()] = $partnerAccountReport;
            }

            $partnerAccountReport = $partnerAccountReports[$adNetwork->getPublisherId()];
            $this->aggregatePartnerReport($partnerAccountReport, $adNetworkReport);
        }

        return $partnerAccountReports;
    }

    /**
     * do getting reports
     *
     * @param ReportTypeInterface $reportType
     * @return mixed
     */
    public function getReports(ReportTypeInterface $reportType)
    {
        $selector = $this->getSelectorFor($reportType);

        return $selector->getReports($reportType, $this->reportDate, $this->reportDate);
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return SelectorInterface
     * @throws RuntimeException
     */
    protected function getSelectorFor(ReportTypeInterface $reportType)
    {
        foreach ($this->selectors as $selector) {
            if ($selector->supportsReportType($reportType)) {
                return $selector;
            }
        }

        throw new RuntimeException('cannot find a selector for this report type');
    }
}