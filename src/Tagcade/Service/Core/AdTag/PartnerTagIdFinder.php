<?php


namespace Tagcade\Service\Core\AdTag;



use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Core\AdTagRepositoryInterface;

class PartnerTagIdFinder implements PartnerTagIdFinderInterface
{
    const REVENUE_OPTION_NONE = 0;
    const REVENUE_OPTION_CPM_FIXED = 1;
    const REVENUE_OPTION_CPM_PERCENT = 2;

    const TAG_COUNT_KEY = 'tagCount';
    const TAGS_KEY = 'tags';
    const DOMAIN_KEY = 'domain';
    const REVENUE_CONFIG_KEY = 'revenueConfig';
    const REVENUE_CONFIG_OPTION_KEY = 'option';
    const REVENUE_CONFIG_VALUE_KEY = 'value';
    const SUB_PUBLISHER_ID_KEY = 'id';
    const SUB_PUBLISHER_KEY = 'subPublisher';
    const DOMAIN_COUNT_KEY = 'domainCount';

    /**
     * @var AdTagRepositoryInterface
     */
    protected $adTagRepository;

    /**
     * PartnerTagIdFinder constructor.
     * @param AdTagRepositoryInterface $adTagRepository
     */
    public function __construct(AdTagRepositoryInterface $adTagRepository)
    {
        $this->adTagRepository = $adTagRepository;
    }

    public function getTcTag(AdNetworkPartnerInterface $adNetworkPartner, UserRoleInterface $publisher, $partnerTagId)
    {
        $foundAdTags = $this->adTagRepository->getAdTagsForPartner($adNetworkPartner, $publisher, $partnerTagId);

        $formattedResult = [
            self::TAG_COUNT_KEY => count($foundAdTags)
        ];

        $sites = [];
        foreach ($foundAdTags as $adTag) {
            /** @var AdTagInterface $adTag */
            $site = $adTag->getAdSlot()->getSite();
            $domain = $site->getDomain();
            $subPublisher = $site->getSubPublisher();
            $subPublisherId = $subPublisher instanceof SubPublisherInterface ? $subPublisher->getId() : null;
            $sites[$domain] = true;

            $tag = [
                self::SUB_PUBLISHER_ID_KEY => $adTag->getId(),
                self::DOMAIN_KEY => $domain,
                self::SUB_PUBLISHER_KEY => $subPublisherId,
                self::REVENUE_CONFIG_KEY => $subPublisherId != null ? $this->getRevenueShareConfigForSubPublisherAndNetworkPartner($subPublisher, $adTag->getAdNetwork()->getNetworkPartner()) : []
            ];

            $formattedResult[self::TAGS_KEY][] = $tag;
        }

        $formattedResult[self::DOMAIN_COUNT_KEY] = count($sites);

        return $formattedResult;
    }

    protected function getRevenueShareConfigForSubPublisherAndNetworkPartner(SubPublisherInterface $subPublisher, AdNetworkPartnerInterface $partner)
    {
        $revenueShareConfig[self::REVENUE_CONFIG_OPTION_KEY] = self::REVENUE_OPTION_NONE; //default
        $revenueShareConfig[self::REVENUE_CONFIG_VALUE_KEY] = 0;

        foreach ($subPublisher->getSubPublisherPartnerRevenue() as $partnerRevenueConfig) {
            /**
             * @var SubPublisherPartnerRevenueInterface $partnerRevenueConfig
             */
            $networkPartner = $partnerRevenueConfig->getAdNetworkPartner();
            if (!$networkPartner instanceof AdNetworkPartnerInterface || $networkPartner->getId() != $partner->getId()) {
                continue;
            }

            $revenueShareConfig[self::REVENUE_CONFIG_OPTION_KEY] = $partnerRevenueConfig->getRevenueOption();
            $revenueShareConfig[self::REVENUE_CONFIG_VALUE_KEY] = $partnerRevenueConfig->getRevenueValue();
        }

        return $revenueShareConfig;
    }
}