<?php

namespace Tagcade\Service\Report\UnifiedReport;


use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AccountReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AdNetworkAdTagReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AdNetworkDomainReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\AdNetworkReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReport;
use Tagcade\Entity\Report\UnifiedReport\Comparison\SubPublisherReport;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\CalculateComparisonRatiosTrait;
use Tagcade\Model\Report\UnifiedReport\Network\NetworkAdTagReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\AdNetworkRepositoryInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReportRepositoryInterface as TcAdNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AccountReportRepositoryInterface as TcAccountReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkAdTagReportRepositoryInterface as TcAdNetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagReportRepositoryInterface as TcAdNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainAdTagSubPublisherReportRepositoryInterface as TcSubPublisherNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Partner\AdNetworkDomainReportRepositoryInterface as TcAdNetworkDomainReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherAdNetworkReportRepositoryInterface as TcSubPublisherAdNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\SubPublisher\SubPublisherReportRepositoryInterface as TcSubPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AccountReportRepositoryInterface as ComparisonAccountRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkAdTagReportRepositoryInterface as ComparisonAdNetworkAdTagRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagReportRepositoryInterface as ComparisonAdNetworkDomainAdTagRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainAdTagSubPublisherReportRepositoryInterface as ComparisonAdNetworkDomainAdTagSubPublisherRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkDomainReportRepositoryInterface as ComparisonAdNetworkDomainRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\AdNetworkReportRepositoryInterface as ComparisonAdNetworkRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\SubPublisherAdNetworkReportRepositoryInterface as ComparisonSubPublisherAdNetworkRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Comparison\SubPublisherReportRepositoryInterface as ComparisonSubPublisherRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface as UnifiedNetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagReportRepositoryInterface as UnifiedNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkDomainAdTagSubPublisherReportRepositoryInterface as UnifiedSubPublisherNetworkDomainAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkReportRepositoryInterface as UnifiedNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface as UnifiedNetworkSiteReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\PublisherReportRepositoryInterface as UnifiedPublisherReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherNetworkReportRepositoryInterface as UnifiedSubPublisherNetworkReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Publisher\SubPublisherReportRepositoryInterface as UnifiedSubPublisherReportRepositoryInterface;

class ReportComparisonCreator implements ReportComparisonCreatorInterface
{
    use CalculateComparisonRatiosTrait;

    /** @var EntityManagerInterface */
    private $em;
    /** @var AdTagRepositoryInterface */
    private $adTagRepository;
    /** @var AdNetworkRepositoryInterface */
    private $adNetworkRepository;
    /** @var SubPublisherManagerInterface */
    private $subPublisherManager;

    /* all report repositories of tagcade and unifiedReport in pairs */
    /** @var TcAdNetworkAdTagReportRepositoryInterface */
    private $tcAdTagRepository;
    /** @var UnifiedNetworkAdTagReportRepositoryInterface */
    private $unifiedAdTagRepository;

    /** @var TcAdNetworkReportRepositoryInterface */
    private $tcAdNetworkRepository;
    /** @var UnifiedNetworkReportRepositoryInterface */
    private $unifiedAdNetworkRepository;

    /** @var TcAdNetworkDomainReportRepositoryInterface */
    private $tcDomainRepository;
    /** @var UnifiedNetworkSiteReportRepositoryInterface */
    private $unifiedDomainRepository;

    /** @var TcAdNetworkDomainAdTagReportRepositoryInterface */
    private $tcDomainAdTagRepository;
    /** @var UnifiedNetworkDomainAdTagReportRepositoryInterface */
    private $unifiedDomainAdTagRepository;

    /** @var TcAccountReportRepositoryInterface */
    private $tcAccountRepository;
    /** @var UnifiedPublisherReportRepositoryInterface */
    private $unifiedPublisherRepository;

    /** @var TcSubPublisherAdNetworkReportRepositoryInterface */
    private $tcSubPublisherAdNetworkRepository;
    /** @var UnifiedSubPublisherNetworkReportRepositoryInterface */
    private $unifiedSubPublisherNetworkRepository;

    /** @var TcSubPublisherReportRepositoryInterface */
    private $tcSubPublisherReportRepository;
    /** @var UnifiedSubPublisherReportRepositoryInterface */
    private $unifiedSubPublisherReportRepository;

    /** @var TcSubPublisherNetworkDomainAdTagReportRepositoryInterface */
    private $tcSubPublisherDomainAdTagRepository;
    /** @var UnifiedSubPublisherNetworkDomainAdTagReportRepositoryInterface */
    private $unifiedSubPublisherDomainAdTagRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var ComparisonAccountRepositoryInterface */
    private $comparisonAccountRepository;

    /** @var ComparisonAdNetworkAdTagRepositoryInterface */
    private $comparisonAdNetworkAdTagRepository;

    /** @var ComparisonAdNetworkDomainAdTagRepositoryInterface */
    private $comparisonAdNetworkDomainAdTagRepository;

    /** @var ComparisonAdNetworkDomainAdTagSubPublisherRepositoryInterface */
    private $comparisonAdNetworkDomainAdTagSubPublisherRepository;

    /** @var ComparisonAdNetworkDomainRepositoryInterface */
    private $comparisonAdNetworkDomainRepository;

    /** @var ComparisonAdNetworkRepositoryInterface */
    private $comparisonAdNetworkRepository;

    /** @var ComparisonSubPublisherRepositoryInterface */
    private $comparisonSubPublisherRepository;

    /** @var ComparisonSubPublisherAdNetworkRepositoryInterface */
    private $comparisonSubPublisherNetworkRepository;

    /**
     * ReportComparisonCreator constructor.
     * @param EntityManagerInterface $em
     * @param AdTagRepositoryInterface $adTagRepository
     * @param AdNetworkRepositoryInterface $adNetworkRepository
     * @param SubPublisherManagerInterface $subPublisherManager
     * @param TcAdNetworkAdTagReportRepositoryInterface $tcAdTagRepository
     * @param UnifiedNetworkAdTagReportRepositoryInterface $unifiedAdTagRepository
     * @param TcAdNetworkDomainReportRepositoryInterface $tcDomainRepository
     * @param UnifiedNetworkSiteReportRepositoryInterface $unifiedDomainRepository
     * @param TcAdNetworkReportRepositoryInterface $tcAdNetworkRepository
     * @param UnifiedNetworkReportRepositoryInterface $unifiedAdNetworkRepository
     * @param TcAccountReportRepositoryInterface $tcAccountRepository
     * @param UnifiedPublisherReportRepositoryInterface $unifiedPublisherRepository
     * @param TcSubPublisherAdNetworkReportRepositoryInterface $tcSubPublisherAdNetworkRepository
     * @param UnifiedSubPublisherNetworkReportRepositoryInterface $unifiedSubPublisherNetworkRepository
     * @param TcSubPublisherReportRepositoryInterface $tcSubPublisherReportRepository
     * @param UnifiedSubPublisherReportRepositoryInterface $unifiedSubPublisherReportRepository
     * @param TcAdNetworkDomainAdTagReportRepositoryInterface $tcDomainAdTagRepository
     * @param UnifiedNetworkDomainAdTagReportRepositoryInterface $unifiedDomainAdTagRepository
     * @param TcSubPublisherNetworkDomainAdTagReportRepositoryInterface $tcSubPublisherDomainAdTagRepository
     * @param UnifiedSubPublisherNetworkDomainAdTagReportRepositoryInterface $unifiedSubPublisherDomainAdTagRepository
     * @param ComparisonAccountRepositoryInterface $comparisonAccountRepository
     * @param ComparisonAdNetworkAdTagRepositoryInterface $comparisonAdNetworkAdTagRepository
     * @param ComparisonAdNetworkDomainAdTagRepositoryInterface $comparisonAdNetworkDomainAdTagRepository
     * @param ComparisonAdNetworkDomainAdTagSubPublisherRepositoryInterface $comparisonAdNetworkDomainAdTagSubPublisherRepository
     * @param ComparisonAdNetworkDomainRepositoryInterface $comparisonAdNetworkDomainRepository
     * @param ComparisonAdNetworkRepositoryInterface $comparisonAdNetworkRepository
     * @param ComparisonSubPublisherRepositoryInterface $comparisonSubPublisherRepository
     * @param ComparisonSubPublisherAdNetworkRepositoryInterface $comparisonSubPublisherNetworkRepository
     */
    function __construct(EntityManagerInterface $em, AdTagRepositoryInterface $adTagRepository, AdNetworkRepositoryInterface $adNetworkRepository, SubPublisherManagerInterface $subPublisherManager,
                         TcAdNetworkAdTagReportRepositoryInterface $tcAdTagRepository, UnifiedNetworkAdTagReportRepositoryInterface $unifiedAdTagRepository,
                         TcAdNetworkDomainReportRepositoryInterface $tcDomainRepository, UnifiedNetworkSiteReportRepositoryInterface $unifiedDomainRepository,
                         TcAdNetworkReportRepositoryInterface $tcAdNetworkRepository, UnifiedNetworkReportRepositoryInterface $unifiedAdNetworkRepository,
                         TcAccountReportRepositoryInterface $tcAccountRepository, UnifiedPublisherReportRepositoryInterface $unifiedPublisherRepository,
                         TcSubPublisherAdNetworkReportRepositoryInterface $tcSubPublisherAdNetworkRepository, UnifiedSubPublisherNetworkReportRepositoryInterface $unifiedSubPublisherNetworkRepository,
                         TcSubPublisherReportRepositoryInterface $tcSubPublisherReportRepository, UnifiedSubPublisherReportRepositoryInterface $unifiedSubPublisherReportRepository,
                         TcAdNetworkDomainAdTagReportRepositoryInterface $tcDomainAdTagRepository, UnifiedNetworkDomainAdTagReportRepositoryInterface $unifiedDomainAdTagRepository,
                         TcSubPublisherNetworkDomainAdTagReportRepositoryInterface $tcSubPublisherDomainAdTagRepository, UnifiedSubPublisherNetworkDomainAdTagReportRepositoryInterface $unifiedSubPublisherDomainAdTagRepository,
                         ComparisonAccountRepositoryInterface $comparisonAccountRepository, ComparisonAdNetworkAdTagRepositoryInterface $comparisonAdNetworkAdTagRepository,
                         ComparisonAdNetworkDomainAdTagRepositoryInterface $comparisonAdNetworkDomainAdTagRepository, ComparisonAdNetworkDomainAdTagSubPublisherRepositoryInterface $comparisonAdNetworkDomainAdTagSubPublisherRepository,
                         ComparisonAdNetworkDomainRepositoryInterface $comparisonAdNetworkDomainRepository, ComparisonAdNetworkRepositoryInterface $comparisonAdNetworkRepository,
                         ComparisonSubPublisherRepositoryInterface $comparisonSubPublisherRepository, ComparisonSubPublisherAdNetworkRepositoryInterface $comparisonSubPublisherNetworkRepository
    )
    {
        $this->em = $em;
        $this->tcAdTagRepository = $tcAdTagRepository;
        $this->adTagRepository = $adTagRepository;
        $this->unifiedAdTagRepository = $unifiedAdTagRepository;
        $this->tcAdNetworkRepository = $tcAdNetworkRepository;
        $this->unifiedAdNetworkRepository = $unifiedAdNetworkRepository;
        $this->tcDomainRepository = $tcDomainRepository;
        $this->unifiedDomainRepository = $unifiedDomainRepository;
        $this->tcAccountRepository = $tcAccountRepository;
        $this->unifiedPublisherRepository = $unifiedPublisherRepository;
        $this->adNetworkRepository = $adNetworkRepository;
        $this->subPublisherManager = $subPublisherManager;
        $this->tcSubPublisherAdNetworkRepository = $tcSubPublisherAdNetworkRepository;
        $this->unifiedSubPublisherNetworkRepository = $unifiedSubPublisherNetworkRepository;
        $this->tcSubPublisherReportRepository = $tcSubPublisherReportRepository;
        $this->unifiedSubPublisherReportRepository = $unifiedSubPublisherReportRepository;
        $this->tcDomainAdTagRepository = $tcDomainAdTagRepository;
        $this->unifiedDomainAdTagRepository = $unifiedDomainAdTagRepository;
        $this->tcSubPublisherDomainAdTagRepository = $tcSubPublisherDomainAdTagRepository;
        $this->unifiedSubPublisherDomainAdTagRepository = $unifiedSubPublisherDomainAdTagRepository;

        $this->comparisonAccountRepository = $comparisonAccountRepository;
        $this->comparisonAdNetworkAdTagRepository = $comparisonAdNetworkAdTagRepository;
        $this->comparisonAdNetworkDomainAdTagRepository = $comparisonAdNetworkDomainAdTagRepository;
        $this->comparisonAdNetworkDomainAdTagSubPublisherRepository = $comparisonAdNetworkDomainAdTagSubPublisherRepository;
        $this->comparisonAdNetworkDomainRepository = $comparisonAdNetworkDomainRepository;
        $this->comparisonAdNetworkRepository = $comparisonAdNetworkRepository;
        $this->comparisonSubPublisherRepository = $comparisonSubPublisherRepository;
        $this->comparisonSubPublisherNetworkRepository = $comparisonSubPublisherNetworkRepository;
    }

    public function updateComparisonForPublisher(
        PublisherInterface $publisher,
        \DateTime $startDate,
        \DateTime $endDate,
        $override = false
    )
    {
        $interval = new \DateInterval('P1D');
        $tmpEndDate = clone $endDate;
        $tmpEndDate->modify('+1 day'); // to include last day

        $adNetworks = $this->adNetworkRepository->getAdNetworksThatHavePartnerForPublisher($publisher);
        $subPublishers = $this->subPublisherManager->allForPublisher($publisher);

        $dateRange = new \DatePeriod($startDate, $interval, $tmpEndDate);
        foreach ($dateRange as $date) {

            foreach ($adNetworks as $adNetwork) {
                $this->createAdNetworkHierarchyComparisonForAdNetwork($date, $adNetwork, $override);
            }

            // Step 4. update account report
            $tcAccountReport = $this->tcAccountRepository->getReportFor($publisher, $date, $date, $oneOrNull = true);
            $unifiedAccountReport = $this->unifiedPublisherRepository->getReportFor($publisher, $date, $date, $oneOrNull = true);

            $accountComparison = new AccountReport();

            $accountComparison
                ->setUnifiedAccountReport($unifiedAccountReport)
                ->setPerformanceAccountReport($tcAccountReport)
                ->setDate($date)
                ->setPublisher($publisher);

            $override === false ? $this->em->persist($accountComparison) : $this->comparisonAccountRepository->override($accountComparison);

            foreach ($subPublishers as $subPublisher) {
                /** @var SubPublisherInterface $subPublisher */
                $this->updateComparisonForSubPublisher($subPublisher, $date, $override);
            }

            $this->em->flush();
        }
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function updateComparisonForSubPublisher(SubPublisherInterface $subPublisher, \DateTime $date, $override = false)
    {
        // update for sub publisher ad network
        $allNetworks = $this->adNetworkRepository->getAdNetworksThatHavePartnerForSubPublisher($subPublisher);
        foreach ($allNetworks as $adNetwork) {
            /**
             * @var AdNetworkInterface $adNetwork
             */
            $tcSubPublisherNetworkReport = $this->tcSubPublisherAdNetworkRepository->getReportFor($subPublisher, $adNetwork, $date, $date, $oneOrNull = true);
            $unifiedSubPublisherNetworkReport = $this->unifiedSubPublisherNetworkRepository->getReportFor($subPublisher, $adNetwork, $date, $date, $oneOrNull = true);

            $comparisonReport = new SubPublisherAdNetworkReport();
            $comparisonReport->setDate($date);
            $comparisonReport->setAdNetwork($adNetwork);
            $comparisonReport->setSubPublisher($subPublisher);
            $comparisonReport->setPerformanceSubPublisherAdNetworkReport($tcSubPublisherNetworkReport);
            $comparisonReport->setName($adNetwork->getName());
            $comparisonReport->setUnifiedSubPublisherAdNetworkReport($unifiedSubPublisherNetworkReport);

            $override === false ? $this->em->persist($comparisonReport) : $this->comparisonSubPublisherNetworkRepository->override($comparisonReport);

            $importedKeys = [];

            $sites = $subPublisher->getSites();
            foreach ($sites as $site) {
                /** @var AdTagInterface[] $adTags */
                $adTags = $this->adTagRepository->getAdTagsForAdNetworkAndSiteWithSubPublisher($adNetwork, $site, $subPublisher);

                foreach ($adTags as $adTag) {
                    $partnerTagId = $adTag->getPartnerTagId();

                    // only support if tag has partnerTagId for mapping
                    if ($partnerTagId == null) {
                        continue;
                    }

                    $importedKey = sprintf('sub_%d_domain_%s_tag_%s', $subPublisher->getId(), $site->getDomain(), $partnerTagId);
                    if (array_key_exists($importedKey, $importedKeys)) {
                        continue;
                    }

                    $importedKeys[$importedKey] = true;

                    $tcSubPublisherNetworkDomainAdTagReport = $this->tcSubPublisherDomainAdTagRepository->getReportFor($adNetwork, $site->getDomain(), $partnerTagId, $subPublisher, $date, $date, $oneOrNull = true);
                    $unifiedSubPublisherNetworkDomainAdTagReport = $this->unifiedSubPublisherDomainAdTagRepository->getReportFor($adNetwork, $site->getDomain(), $partnerTagId, $subPublisher, $date, $date, $oneOrNull = true);
                    $subPublisherDomainAdTagComparisonReport = new AdNetworkDomainAdTagSubPublisherReport();
                    $subPublisherDomainAdTagComparisonReport->setDate($date);
                    $subPublisherDomainAdTagComparisonReport->setAdNetwork($adNetwork);
                    $subPublisherDomainAdTagComparisonReport->setSubPublisher($subPublisher);
                    $subPublisherDomainAdTagComparisonReport->setName($partnerTagId);
                    $subPublisherDomainAdTagComparisonReport->setDomain($site->getDomain());
                    $subPublisherDomainAdTagComparisonReport->setPartnerTagId($partnerTagId);
                    $subPublisherDomainAdTagComparisonReport->setPerformanceAdNetworkDomainAdTagSubPublisherReport($tcSubPublisherNetworkDomainAdTagReport);
                    $subPublisherDomainAdTagComparisonReport->setUnifiedAdNetworkDomainAdTagSubPublisherReport($unifiedSubPublisherNetworkDomainAdTagReport);

                    //echo sprintf('Date %s : Inserting for sub-publisher %d, domain %s, ad tag %s' . "\n", $date->format('Y-m-d'), $subPublisher->getId(), $site->getDomain(), $adTag->getPartnerTagId());
                    $override === false ? $this->em->persist($subPublisherDomainAdTagComparisonReport) : $this->comparisonAdNetworkDomainAdTagSubPublisherRepository->override($subPublisherDomainAdTagComparisonReport);
                }
            }
        }

        // update for sub publisher
        $tcSubPublisherReport = $this->tcSubPublisherReportRepository->getReportFor($subPublisher, $date, $date, $oneOrNull = true);
        $unifiedSubPublisherReport = $this->unifiedSubPublisherReportRepository->getReportFor($subPublisher, $date, $date, $oneOrNull = true);

        $comparisonReport = new SubPublisherReport();
        $comparisonReport->setName($subPublisher->getUser()->getUsername());
        $comparisonReport->setSubPublisher($subPublisher);
        $comparisonReport->setDate($date);
        $comparisonReport->setPerformanceSubPublisherReport($tcSubPublisherReport);
        $comparisonReport->setUnifiedSubPublisherReport($unifiedSubPublisherReport);

        $override === false ? $this->em->persist($comparisonReport) : $this->comparisonSubPublisherRepository->override($comparisonReport);

        if ($override === false) {
            try {
                $this->em->flush();
            } catch (UniqueConstraintViolationException $ex) {
                throw $ex;
            }
        }
    }

    private function createAdNetworkHierarchyComparisonForAdNetwork(\DateTime $date, AdNetworkInterface $adNetwork, $override = false)
    {
        $adTags = $this->adTagRepository->getAdTagsThatHavePartnerConfigForAdNetwork($adNetwork);

        // Step 1, 2. update ad tag, domain ad tag report
        $this->createAdTagComparisonForDate($date, $adNetwork, $adTags, $override);

        // Step 3. update ad network,  domain report
        $domains = $this->getDomainsForAdTags($adTags);
        $this->createAdNetworkDomainComparisonForDate($date, $adNetwork, $domains, $override);

        // Step 4. update network report
        $this->createSimpleAdNetworkComparisonForDate($date, $adNetwork, $override);
    }

    private function createSimpleAdNetworkComparisonForDate(\DateTime $date, AdNetworkInterface $adNetwork, $override = false)
    {
        $tcNetworkReport = $this->tcAdNetworkRepository->getReportFor($adNetwork, $date, $date, $oneOrNull = true);
        $unifiedAdNetworkReport = $this->unifiedAdNetworkRepository->getReportFor($adNetwork, $date, $date, $oneOrNull = true);

        $networkComparison = new AdNetworkReport();
        $networkComparison
            ->setUnifiedAdNetworkReport($unifiedAdNetworkReport)
            ->setPerformanceAdNetworkReport($tcNetworkReport)
            ->setDate($date)
            ->setAdNetwork($adNetwork)
            ->setName($adNetwork->getName());

        $override === false ? $this->em->persist($networkComparison) : $this->comparisonAdNetworkRepository->override($networkComparison);
    }

    private function createAdNetworkDomainComparisonForDate(\DateTime $date, AdNetworkInterface $adNetwork, $domains, $override = false)
    {
        foreach ($domains as $domain => $value) {
            $tcDomainReport = $this->tcDomainRepository->getReportFor($adNetwork, $domain, $date, $date, $oneOrNull = true);
            $unifiedDomainReport = $this->unifiedDomainRepository->getReportFor($adNetwork, $domain, $date, $date, $oneOrNull = true);

            $domainComparisonReport = new AdNetworkDomainReport();
            $domainComparisonReport
                ->setUnifiedNetworkSiteReport($unifiedDomainReport)
                ->setPerformanceAdNetworkDomainReport($tcDomainReport)
                ->setDate($date)
                ->setDomain($domain)
                ->setAdNetwork($adNetwork)
                ->setName($domain);

            $override === false ? $this->em->persist($domainComparisonReport) : $this->comparisonAdNetworkDomainRepository->override($domainComparisonReport);
        }
    }

    protected function createAdTagComparisonForDate(\DateTime $date, AdNetworkInterface $adNetwork, array $adTags, $override = false)
    {
        $domains = [];
        $partnerTagIds = [];

        $this->logger->info(sprintf("processing... date %s\n", $date->format('Y-m-d')));

        /** @var AdTagInterface $adTag */
        foreach ($adTags as $adTag) {
            if (!$adTag instanceof AdTagInterface) {
                continue;
            }
            $partnerTagId = $adTag->getPartnerTagId();
            if (array_key_exists($partnerTagId, $partnerTagIds)) {
                $this->logger->info(sprintf('Processed ad tag %d with partner tag id %s', $adTag->getId(), $partnerTagId));
                continue;
            }

            $this->doCompareAdNetworkAdTagReport($date, $adNetwork, $partnerTagId, $override);

            $partnerTagIds[$partnerTagId] = $partnerTagId;

            $domain = $adTag->getAdSlot()->getSite()->getDomain();
            if (!isset($domains[$domain])) {
                $domains[$domain] = [];
            }

            if (array_key_exists($partnerTagId, $domains[$domain])) {
                continue;
            }

            $this->doCompareAdNetworkDomainAdTagReport($date, $adNetwork, $domain, $partnerTagId, $override);

            $domains[$domain][$partnerTagId] = $partnerTagId;
        }
    }

    private function doCompareAdNetworkDomainAdTagReport(\DateTime $date, AdNetworkInterface $adNetwork, $domain, $partnerTagId, $override = false)
    {
        $tcDomainAdTagReport = $this->tcDomainAdTagRepository->getReportFor($adNetwork, $domain, $partnerTagId, $date, $date, $oneOrNull = true);
        $unifiedDomainAdTagReport = $this->unifiedDomainAdTagRepository->getReportFor($adNetwork, $domain, $partnerTagId, $date, $date, $oneOrNull = true);

        $domainAdTagComparisonReport = new AdNetworkDomainAdTagReport();

        $domainAdTagComparisonReport
            ->setUnifiedAdNetworkDomainAdTagReport($unifiedDomainAdTagReport)
            ->setPerformanceAdNetworkDomainAdTagReport($tcDomainAdTagReport)
            ->setDate($date)
            ->setPartnerTagId($partnerTagId)
            ->setAdNetwork($adNetwork)
            ->setDomain($domain)
            ->setName($partnerTagId);

        $override === false ? $this->em->persist($domainAdTagComparisonReport) : $this->comparisonAdNetworkDomainAdTagRepository->override($domainAdTagComparisonReport);
    }

    private function doCompareAdNetworkAdTagReport(\DateTime $date, AdNetworkInterface $adNetwork, $partnerTagId, $override = false)
    {
        $tcAdTagReport = $this->tcAdTagRepository->getReportFor($adNetwork, $partnerTagId, $date, $date, $oneOrNull = true);
        /** @var NetworkAdTagReportInterface $unifiedAdTagReport */
        $unifiedAdTagReport = $this->unifiedAdTagRepository->getReportFor($adNetwork, $partnerTagId, $date, $date, $oneOrNull = true);

        $adTagComparisonReport = new AdNetworkAdTagReport();
        $adTagComparisonReport
            ->setUnifiedAdNetworkAdTagReport($unifiedAdTagReport)
            ->setPerformanceAdNetworkAdTagReport($tcAdTagReport)
            ->setDate($date)
            ->setPartnerTagId($partnerTagId)
            ->setAdNetwork($adNetwork)
            ->setName($partnerTagId);

        $override === false ? $this->em->persist($adTagComparisonReport) : $this->comparisonAdNetworkAdTagRepository->override($adTagComparisonReport);
    }

    protected function validReportsForComparisonCreation($tcReport, $unifiedReport)
    {
        if ($tcReport == null || $unifiedReport == null) {
            throw new \Exception('Either tagcade or unified report is null. Please make sure the reports are available before comparing.');
        }

        return $tcReport != null && $unifiedReport != null;
    }

    protected function getDomainsForAdTags(array $adTags)
    {
        $domains = [];
        foreach ($adTags as $adTag) {
            /** @var AdTagInterface $adTag */
            $adSlot = $adTag->getAdSlot();
            if (!$adSlot instanceof BaseAdSlotInterface) {
                continue;
            }

            $site = $adSlot->getSite();
            if (!$site instanceof SiteInterface) {
                continue;
            }

            if (!array_key_exists($site->getDomain(), $domains)) {
                $domains[$site->getDomain()] = true;
            }
        }

        return $domains;
    }
}