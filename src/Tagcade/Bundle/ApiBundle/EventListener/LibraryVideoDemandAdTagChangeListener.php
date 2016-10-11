<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Behaviors\ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
use Tagcade\Cache\Video\DomainListManagerInterface;
use Tagcade\Entity\Core\Blacklist;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\WhiteList;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\Core\LibraryVideoDemandAdTag;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\WhiteListInterface;


class LibraryVideoDemandAdTagChangeListener
{
    use ValidateVideoDemandAdTagAgainstPlacementRuleTrait;
    /**
     * @var DomainListManagerInterface
     */
    private $domainListManager;

    /**
     * @var array
     */
    private $builtinBlacklists;

    /**
     * VideoDemandAdTagChangeListener constructor.
     * @param DomainListManagerInterface $domainListManager
     * @param array $builtinBlacklists
     */
    public function __construct(DomainListManagerInterface $domainListManager, array $builtinBlacklists)
    {
        $this->domainListManager = $domainListManager;
        $this->builtinBlacklists = $builtinBlacklists;
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();
        if (!$entity instanceof LibraryVideoDemandAdTagInterface) {
            return;
        }

        $this->validateTargeting($em, $entity);
    }

    /**
     * handle event preUpdate to detect which site is been changing on 'domain', then update site_token to make sure site_token is unique for domain & publisher
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getObject();

        if (!$entity instanceof LibraryVideoDemandAdTagInterface) {
            return;
        }

        if ($args->hasChangedField('targeting')) {
            $this->validateTargeting($em, $entity);
        }

        if ($args->hasChangedField('sellPrice')) {
            $this->autoPauseVideoDemandAdTags($em, $entity);
        }
    }

    protected function autoPauseVideoDemandAdTags(EntityManagerInterface $em, LibraryVideoDemandAdTagInterface $libraryDemandAdTag)
    {
        $videoDemandAdTags = $libraryDemandAdTag->getVideoDemandAdTags();
        /** @var VideoDemandAdTagInterface $videoDemandAdTag */
        foreach($videoDemandAdTags as $videoDemandAdTag) {
            if ($this->validateDemandAdTagAgainstPlacementRule($videoDemandAdTag) === false) {
                $videoDemandAdTag->setActive(VideoDemandAdTag::AUTO_PAUSED);
            }
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param LibraryVideoDemandAdTagInterface $libraryDemandAdTag
     */
    protected function validateTargeting(EntityManagerInterface $em, LibraryVideoDemandAdTagInterface $libraryDemandAdTag)
    {
        $targeting = $libraryDemandAdTag->getTargeting();

        if (!is_array($targeting)) {
            return;
        }

        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_COUNTRIES, $targeting) && !is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_COUNTRIES])) {
            throw new InvalidArgumentException(sprintf('expect "countries" to be an array, got "%s"', gettype($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_COUNTRIES])));
        }

        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_COUNTRIES, $targeting) && !is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_COUNTRIES])) {
            throw new InvalidArgumentException(sprintf('expect "excludeCountries" to be an array, got "%s"', gettype($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_COUNTRIES])));
        }

        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS, $targeting) && !is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS])) {
            throw new InvalidArgumentException(sprintf('expect "excludeDomains" to be an array, got "%s"', gettype($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS])));
        }

        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS, $targeting) && !is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS])) {
            throw new InvalidArgumentException(sprintf('expect "domains" to be an array, got "%s"', gettype($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS])));
        }

        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM, $targeting) && !is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM]) ) {
            throw new InvalidArgumentException(sprintf('expect "platform" to be an array, got "%s"', gettype($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM])));
        }


        if (array_key_exists(LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM, $targeting) &&
            is_array($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM])
        ) {
            $diff = array_diff($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM], [LibraryVideoDemandAdTag::PLATFORM_FLASH, LibraryVideoDemandAdTag::PLATFORM_JAVASCRIPT]);

            if (count($diff) > 0) {
                throw new InvalidArgumentException(sprintf('platform "%" is not supported', implode(',', $diff)));
            }

//            $videoDemandAdTags = $libraryDemandAdTag->getVideoDemandAdTags();
//            if (count($videoDemandAdTags) > 0) {
//                /** @var VideoDemandAdTagInterface $videoDemandAdTag */
//                foreach($videoDemandAdTags as $videoDemandAdTag) {
//                    $videoWaterfallTagItem = $videoDemandAdTag->getVideoWaterfallTagItem();
//
//                    if ($videoWaterfallTagItem instanceof VideoWaterfallTagItemInterface) {
//                        $videoWaterfallTag = $videoWaterfallTagItem->getVideoWaterfallTag();
//                        $adTagPlatform = $videoWaterfallTag->getPlatform();
//                        $diff = array_diff($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_PLATFORM], $adTagPlatform);
//                        if (count($diff) > 0) {
//                            throw new InvalidArgumentException(sprintf('The video ad tag possess this ad source doesn\'t have platform "%s"', implode(',', $diff)));
//                        }
//                    }
//                }
//            }

        }

        $excludeDomains = [];
        $domains = [];
        if (!isset($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS])) {
            return;
        }

        $blacklists = $targeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS];
        $blackListKeys = [];
        $whiteListKeys = [];
        $blacklistRepository = $em->getRepository(Blacklist::class);
        foreach($blacklists as $suffix) {
            if (is_array($suffix)) {
                $suffix = $suffix[LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY];
            }
            $builtin = false;
            foreach($this->builtinBlacklists as $item) {
                if ($item[LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY] === $suffix) {
                    $blackListKeys[] = array(LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY => $suffix, LibraryVideoDemandAdTag::LIST_DOMAIN_NAME_KEY => $item[LibraryVideoDemandAdTag::LIST_DOMAIN_NAME_KEY]);
                    $excludeDomains = array_merge($excludeDomains, $this->domainListManager->getDomainsForBlacklist($suffix));
                    $builtin = true;
                }
            }

            if ($builtin === true) {
                continue;
            }

            $blacklist = $blacklistRepository->findBlacklistBySuffixKey($suffix);
            if ($blacklist instanceof BlacklistInterface) {
                $blackListKeys[] = array(LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY => $blacklist->getSuffixKey(), LibraryVideoDemandAdTag::LIST_DOMAIN_NAME_KEY => $blacklist->getName());
                $excludeDomains = array_merge($excludeDomains, $blacklist->getDomains());
            }
        }

        if (!isset($targeting[LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS])) {
            return;
        }

        $whiteLists = $targeting[LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS];
        $whiteListRepository = $em->getRepository(WhiteList::class);
        foreach($whiteLists as $suffix) {
            if (is_array($suffix)) {
                $suffix = $suffix[LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY];
            }
            $whiteList = $whiteListRepository->findWhiteListBySuffixKey($suffix);
            if ($whiteList instanceof WhiteListInterface) {
                $whiteListKeys[] = array (LibraryVideoDemandAdTag::LIST_DOMAIN_SUFFIX_KEY => $whiteList->getSuffixKey(), LibraryVideoDemandAdTag::LIST_DOMAIN_NAME_KEY => $whiteList->getName());
                $domains = array_merge($domains, $whiteList->getDomains());
            }
        }

        $overlappedDomains = array_intersect($domains, $excludeDomains);
        if (count($overlappedDomains) > 0) {
            throw new InvalidArgumentException(sprintf('Those domain "%s" can not be belonged to both excluded and included list', implode(',', $overlappedDomains)));
        }

        $newTargeting = $targeting;
        $newTargeting[LibraryVideoDemandAdTag::TARGETING_KEY_DOMAINS] = $whiteListKeys;
        $newTargeting[LibraryVideoDemandAdTag::TARGETING_KEY_EXCLUDE_DOMAINS] = $blackListKeys;
        $libraryDemandAdTag->setTargeting($newTargeting);
    }
}