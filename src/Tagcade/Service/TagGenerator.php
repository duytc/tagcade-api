<?php

namespace Tagcade\Service;

use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdSlot;
use Tagcade\Model\Core\AdSlotAbstractInterface;
use Tagcade\Model\Core\AdSlotInterface;
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
                /** @var AdSlotInterface|NativeAdSlotInterface $adSlot */
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
     * @param AdSlotAbstractInterface $adSlot
     * @return string
     */
    public function createJsTags($adSlot)
    {
        if ($adSlot instanceof DynamicAdSlotInterface) {
            return $this->createDisplayAdTagForDynamicAdSlot($adSlot);
        }

        if ($adSlot instanceof AdSlotInterface) {
            return $this->createDisplayAdTagForAdSlot($adSlot);
        }

        if ($adSlot instanceof NativeAdSlotInterface) {
            return $this->createDisplayAdTagForNativeAdSlot($adSlot);
        }

        throw new RuntimeException(sprintf('Generate ad tag for %s is not supported', get_class($adSlot)));

    }

    /**
     * @param AdSlotInterface $adSlot
     * @return string
     */
    public function createDisplayAdTagForAdSlot(AdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);

        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $adSlot->getSite()->getDomain());
        $tag .= '<script type="text/javascript">' . "\n";
        $tag .= sprintf("var tc_slot = %d;\n", $adSlot->getId());
        $tag .= sprintf("var tc_size = '%dx%d';\n", $adSlot->getWidth(), $adSlot->getHeight());
        $tag .= "</script>\n";
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js"></script>' . "\n", $this->baseTagUrl, $adSlot->getSiteId());

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
        $tag .= '<script type="text/javascript">' . "\n";
        $tag .= sprintf("var tc_slot = %d;\n", $adSlot->getId());

        if ($adSlot->isSupportedNative()) {
            $tag .= "var tc_native = true;\n";
        }

        $tag .= "</script>\n";
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js"></script>' . "\n", $this->baseTagUrl, $adSlot->getSiteId());

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
        $tag .= '<script type="text/javascript">' . "\n";
        $tag .= sprintf("var tc_slot = %d;\n", $nativeAdSlot->getId());
        $tag .= "var tc_native = true;\n";
        $tag .= "</script>\n";
        $tag .= sprintf('<script type="text/javascript" src="%s/2.0/%d/adtag.js"></script>' . "\n", $this->baseTagUrl, $nativeAdSlot->getSiteId());

        return $tag;
    }

    /**
     * @param SiteInterface $site
     * @return string
     */
    public function createDisplayPassbackTag(SiteInterface $site)
    {
        $tag = sprintf('<script type="text/javascript" src="%s/2.0/%d/passback.js"></script>' . "\n", $this->baseTagUrl, $site->getId());

        return $tag;
    }
} 