<?php


namespace Tagcade\Service\Core\Site;


use Tagcade\Bundle\UserBundle\DomainManager\SubPublisherManagerInterface;
use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;
use Tagcade\Service\Core\AdNetwork\AdNetworkServiceInterface;

class SiteService implements SiteServiceInterface
{
    const REVENUE_OPTION_NONE = 0;
    const REVENUE_OPTION_CPM_FIXED = 1;
    const REVENUE_OPTION_CPM_PERCENT = 2;

    /**
     * @var AdNetworkServiceInterface
     */
    protected $adNetworkService;

    /**
     * @var SiteRepositoryInterface
     */
    protected $siteRepository;

    /**
     * @var SubPublisherManagerInterface
     */
    protected $subPublisherManager;

    /**
     * SiteService constructor.
     * @param AdNetworkServiceInterface $adNetworkService
     * @param SiteRepositoryInterface $siteRepository
     * @param SubPublisherManagerInterface $subPublisherManager
     */
    public function __construct(AdNetworkServiceInterface $adNetworkService, SiteRepositoryInterface $siteRepository, SubPublisherManagerInterface $subPublisherManager)
    {
        $this->adNetworkService = $adNetworkService;
        $this->siteRepository = $siteRepository;
        $this->subPublisherManager = $subPublisherManager;
    }

    public function getSubPublisherFromDomain(AdNetworkPartnerInterface $adNetworkPartner, PublisherInterface $publisher, $domain)
    {
        $subPublisherIds = $this->siteRepository->findSubPublisherByDomainFilterPublisher($publisher, $domain);

        $subPublishers = [];
        foreach($subPublisherIds as $subPublisherId) {
            $subPublisher = $this->subPublisherManager->find($subPublisherId);
            if (!$subPublisher instanceof SubPublisherInterface) {
                continue;
            }

            if (!in_array($subPublisher->getId(), $subPublishers)) {
                $revenueShareConfig = $this->getRevenueShareConfigForSubPublisherAndNetworkPartner($subPublisher, $adNetworkPartner);
                $subPublishers[] = [
                    'id' => $subPublisher->getId(),
                    'revenueConfig' => $revenueShareConfig
                ];
            }
        }

        return $subPublishers;
    }

    protected function getRevenueShareConfigForSubPublisherAndNetworkPartner(SubPublisherInterface $subPublisher, AdNetworkPartnerInterface $partner)
    {
        $revenueShareConfig['option'] = self::REVENUE_OPTION_NONE; //default
        $revenueShareConfig['value'] = 0;

        foreach ($subPublisher->getSubPublisherPartnerRevenue() as $partnerRevenueConfig) {
            /**
             * @var SubPublisherPartnerRevenueInterface $partnerRevenueConfig
             */
            $networkPartner = $partnerRevenueConfig->getAdNetworkPartner();
            if (!$networkPartner instanceof AdNetworkPartnerInterface || $networkPartner->getId() != $partner->getId()) {
                continue;
            }

            $revenueShareConfig['option'] = $partnerRevenueConfig->getRevenueOption();
            $revenueShareConfig['value'] = $partnerRevenueConfig->getRevenueValue();
        }

        return $revenueShareConfig;
    }

}