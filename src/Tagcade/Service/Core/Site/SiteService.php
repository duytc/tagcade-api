<?php


namespace Tagcade\Service\Core\Site;


use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
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
     * SiteService constructor.
     * @param AdNetworkServiceInterface $adNetworkService
     */
    public function __construct(AdNetworkServiceInterface $adNetworkService)
    {
        $this->adNetworkService = $adNetworkService;
    }

    public function getSubPublisherFromDomain(AdNetworkPartnerInterface $adNetworkPartner, PublisherInterface $publisher, $domain)
    {
        $sites = $this->adNetworkService->getSitesForPartnerFilterPublisherAndDomain($adNetworkPartner, $publisher, $domain);

        $subPublishers = [];
        foreach ($sites as $st) {
            /**
             * @var SiteInterface $st
             */
            if (!$st->getSubPublisher() instanceof SubPublisherInterface) {
                continue;
            }

            $subPublisher = $st->getSubPublisher();
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