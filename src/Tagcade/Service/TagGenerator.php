<?php

namespace Tagcade\Service;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class TagGenerator
{
    protected $baseTagUrl;

    /**
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseTagUrl = rtrim($baseUrl, '/');
    }

    /**
     * @param SiteInterface $site
     * @return array
     */
    public function getTagsForSite(SiteInterface $site)
    {
        $publisher = $site->getPublisher();

        $tags = [];

        $tags['header'] = $this->createHeaderTag($site);

        if ($publisher->hasDisplayModule()) {
            $tags = array_merge($tags, [
                'display' => [
                    'passback' => $this->createDisplayPassbackTag($site),
                    'ad_slots' => [],
                ],

                'native' => [
                    'ad_slots' => [],
                ],

                'dynamic' => [
                    'ad_slots' => [],
                ]
            ]);

            $allAdSlots = $site->getAllAdSlots();
            foreach($allAdSlots as $adSlot) {
                /** @var DisplayAdSlotInterface|NativeAdSlotInterface $adSlot */
                if (!array_key_exists($adSlot->getType(), $tags)) {
                    continue; // not support generating tags for this ad slot type
                }

                $adSlots = &$tags[$adSlot->getType()]['ad_slots'];
                $adSlots[$adSlot->getName()] = $this->createJsTags($adSlot);
            }

            $removeKeys = [];

            array_walk($tags,
                function ($tagItem, $key) use(&$removeKeys) {
                    if (is_array($tagItem) && count($tagItem['ad_slots']) < 1) {
                        $removeKeys[] = $key;
                    }
                }
            );

            array_walk($removeKeys,
                function($key) use (&$tags) {
                    unset($tags[$key]);
                }
            );
        }

        return $tags;
    }

    /**
     * @param SiteInterface $site
     * @return string
     */
    public function createHeaderTag(SiteInterface $site)
    {
        $tag = sprintf('<script type="text/javascript" src="%s/2.0/%d/tagcade.js"></script>' . "\n", $this->baseTagUrl, $site->getId());

        return $tag;
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @return string
     */
    public function createJsTags($adSlot)
    {
        if ($adSlot instanceof DynamicAdSlotInterface) {
            return $this->createDisplayAdTagForDynamicAdSlot($adSlot);
        }

        if ($adSlot instanceof DisplayAdSlotInterface) {
            return $this->createDisplayAdTagForAdSlot($adSlot);
        }

        if ($adSlot instanceof NativeAdSlotInterface) {
            return $this->createDisplayAdTagForNativeAdSlot($adSlot);
        }

        throw new RuntimeException(sprintf('Generate ad tag for %s is not supported', get_class($adSlot)));

    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return string
     */
    public function createDisplayAdTagForAdSlot(DisplayAdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);

        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $adSlot->getSite()->getDomain());
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d" data-tc-size="%dx%d"></script>' . "\n", $this->baseTagUrl, $adSlot->getSite()->getId(), $adSlot->getId(), $adSlot->getWidth(), $adSlot->getHeight());

        return $tag;
    }

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @return string
     */
    public function createDisplayAdTagForDynamicAdSlot(DynamicAdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);

        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $adSlot->getSite()->getDomain());

        if ($adSlot->isSupportedNative()) {
            $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d" data-tc-slot-type="native"></script>' . "\n", $this->baseTagUrl, $adSlot->getSite()->getId(), $adSlot->getId());
        }
        else {
            $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d"></script>' . "\n", $this->baseTagUrl, $adSlot->getSite()->getId(), $adSlot->getId());
        }

        return $tag;
    }

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @return string
     */
    public function createDisplayAdTagForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot)
    {
        $adSlotName = htmlspecialchars($nativeAdSlot->getName(), ENT_QUOTES);

        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $nativeAdSlot->getSite()->getDomain());
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d" data-tc-slot-type="native"></script>' . "\n", $this->baseTagUrl, $nativeAdSlot->getSite()->getId(), $nativeAdSlot->getId());

        return $tag;
    }

    /**
     * @param SiteInterface $site
     * @return string
     */
    public function createDisplayPassbackTag(SiteInterface $site)
    {
        $tag = "<!-- Tagcade Universal Passback -->\n";
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-passback="true"></script>' . "\n", $this->baseTagUrl, $site->getId());

        return $tag;
    }
} 