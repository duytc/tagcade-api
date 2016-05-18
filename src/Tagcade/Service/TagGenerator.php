<?php

namespace Tagcade\Service;

use Doctrine\Common\Collections\Collection;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\DisplayAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class TagGenerator
{
    const HTTP_PROTOCOL = 'http://';
    const HTTPS_PROTOCOL = '//';
    const PARAMETER_DOMAIN = 'domain';
    const PARAMETER_SECURE = 'secure';

    protected $defaultTagUrl;

    /** @var RonAdSlotManagerInterface */
    private $ronAdSlotManager;

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /**
     * @param array $defaultTagUrl
     * @param RonAdSlotManagerInterface $ronAdSlotManager
     * @param AdSlotManagerInterface $adSlotManager
     */
    public function __construct($defaultTagUrl, RonAdSlotManagerInterface $ronAdSlotManager, AdSlotManagerInterface $adSlotManager)
    {
        $this->defaultTagUrl = $defaultTagUrl;
        $this->ronAdSlotManager = $ronAdSlotManager;
        $this->adSlotManager = $adSlotManager;
    }

    /**
     * get Tags For Passback
     *
     * @param PublisherInterface $publisher
     * @return array
     */
    public function getTagsForPassback(PublisherInterface $publisher)
    {
        // generate Passback js by SubPublisher is also by its Publisher
        if ($publisher instanceof SubPublisherInterface) {
            $publisher = $publisher->getPublisher();
        }

        return array('passback' => $this->createDisplayPassbackTag($publisher));
    }

    /**
     * get Tags For Single RonAdSlot
     *
     * @param RonAdSlotInterface $ronAdSlot
     * @return array
     */
    public function getTagsForSingleRonAdSlot(RonAdSlotInterface $ronAdSlot)
    {
        // create tag for ron slot
        $tags = [
            'jstag' => $this->createJsTags($ronAdSlot),
            'name' => $ronAdSlot->getName()
        ];

        // also create tag for segment of ron slot
        $segments = [];

        /** @var SegmentInterface[] $allSegments */
        $allSegments = $ronAdSlot->getSegments();

        foreach ($allSegments as $segment) {
            $segments[$segment->getName()] = $this->createJsTagForRonAdSlot($ronAdSlot, $segment);
        }

        $tags['segments'] = $segments;

        return $tags;
    }

    /**
     * get RonTags For Publisher
     *
     * @param PublisherInterface $publisher
     * @return array
     */
    public function getRonTagsForPublisher(PublisherInterface $publisher)
    {
        $tags = [];

        $tags = array_merge($tags, [
            'display' => [
                'ad_slots' => [],
            ],

            'native' => [
                'ad_slots' => [],
            ],

            'dynamic' => [
                'ad_slots' => [],
            ]
        ]);
        $ronAdSlots = $this->ronAdSlotManager->getRonAdSlotsForPublisher($publisher);
        foreach ($ronAdSlots as $ronAdSlot) {
            /** @var RonAdSlotInterface $ronAdSlot */
            if (!array_key_exists($ronAdSlot->getLibraryAdSlot()->getLibType(), $tags)) {
                continue; // not support generating tags for this ad slot type
            }

            $adSlots = &$tags[$ronAdSlot->getLibraryAdSlot()->getLibType()]['ad_slots'];
            $adSlots[$ronAdSlot->getId()] = array('jstag' => $this->createJsTags($ronAdSlot));
            $adSlots[$ronAdSlot->getId()]['name'] = $ronAdSlot->getName();
            $segments = &$adSlots[$ronAdSlot->getId()]['segments'];
            $allSegments = $ronAdSlot->getSegments();
            /**
             * @var SegmentInterface $segment
             */
            foreach ($allSegments as $segment) {
                $segments[$segment->getName()] = $this->createJsTagForRonAdSlot($ronAdSlot, $segment);
            }
        }

        $removeKeys = [];

        array_walk($tags,
            function ($tagItem, $key) use (&$removeKeys) {
                if (is_array($tagItem) && count($tagItem['ad_slots']) < 1) {
                    $removeKeys[] = $key;
                }
            }
        );

        array_walk($removeKeys,
            function ($key) use (&$tags) {
                unset($tags[$key]);
            }
        );

        return $tags;
    }

    /**
     * get all Tags For Site
     *
     * @param SiteInterface $site
     * @return array
     */
    public function getTagsForSite(SiteInterface $site)
    {
        /** @var Collection|BaseAdSlotInterface[] $allAdSlots */
        $allAdSlots = $site->getAllAdSlots();

        if ($allAdSlots instanceof Collection) {
            $allAdSlots = $allAdSlots->toArray();
        }

        return $this->getTagsForAdSlots($allAdSlots);
    }

    /**
     * get all Tags For Channel
     *
     * @param ChannelInterface $channel
     * @return array
     */
    public function getTagsForChannel(ChannelInterface $channel)
    {
        $allAdSlots = $this->adSlotManager->getAdSlotsForChannel($channel);

        return $this->getTagsForAdSlots($allAdSlots);
    }

    /**
     * get header of tag for site
     * @param SiteInterface $site
     * @return array
     */
    public function getHeaderForSite(SiteInterface $site)
    {
        return ['header' => $this->createHeaderTagForSite($site)];
    }

    /**
     * get header of tag for publisher
     * @param PublisherInterface $publisher
     * @return array
     */
    public function getHeaderForPublisher(PublisherInterface $publisher)
    {
        return ['header' => $this->createHeaderTagForPublisher($publisher)];
    }

    /**
     * create HeaderTag For Site
     *
     * @param SiteInterface $site
     * @return string
     */
    public function createHeaderTagForSite(SiteInterface $site)
    {
        return sprintf('<script type="text/javascript" src="%s/2.0/%d/tagcade.js"></script>' . "\n", $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId());
    }

    /**
     * create HeaderTag For Publisher
     *
     * @param PublisherInterface $publisher
     * @return string
     */
    public function createHeaderTagForPublisher(PublisherInterface $publisher)
    {
        return sprintf('<script type="text/javascript" src="%s/2.0/tagcade.js"></script>' . "\n", $this->getBaseTagUrlForPublisher($publisher));
    }

    /**
     * @param BaseAdSlotInterface|RonAdSlotInterface $adSlot
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

        if ($adSlot instanceof RonAdSlotInterface) {
            return $this->createJsTagForRonAdSlot($adSlot);
        }

        throw new RuntimeException(sprintf('Generate ad tag for %s is not supported', get_class($adSlot)));
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null $segment
     * @return mixed
     * @throws RuntimeException
     */
    protected function createJsTagForRonAdSlot(RonAdSlotInterface $ronAdSlot, $segment = null)
    {
        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();

        if ($libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            return $this->createDisplayAdTagForRonAdSlot($ronAdSlot, $segment);
        }

        if ($libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
            return $this->createDisplayAdTagForNativeRonAdSlot($ronAdSlot, $segment);
        }

        if ($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
            return $this->createDisplayAdTagForDynamicRonAdSlot($ronAdSlot, $segment);
        }

        throw new RuntimeException(sprintf('Generate ad tag for %s is not supported', get_class($ronAdSlot)));
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null|SegmentInterface $segment
     * @return string
     */
    protected function createDisplayAdTagForRonAdSlot(RonAdSlotInterface $ronAdSlot, $segment = null)
    {
        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof LibraryDisplayAdSlotInterface) {
            throw new RuntimeException('expect a LibraryDisplayAdSlotInterface object');
        }

        $adSlotName = htmlspecialchars($ronAdSlot->getName(), ENT_QUOTES);
        $publisher = $ronAdSlot->getLibraryAdSlot()->getPublisher();
        $publisherUuid = $publisher->getUuid();

        $commentTemplate = "<!-- %s%s -->\n";
        $jsTemplate = '<script type="text/javascript" src="%s/2.0/adtag.js" data-tc-ron-slot="%d" data-tc-size="%dx%d"%s%s></script>' . "\n";

        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $ronAdSlot->getId(), $libraryAdSlot->getWidth(), $libraryAdSlot->getHeight(), $uuidAttribute, '%s');
        } else {
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $ronAdSlot->getId(), $libraryAdSlot->getWidth(), $libraryAdSlot->getHeight(), '', '%s');
        }

        if ($segment instanceof SegmentInterface) {
            $segmentAttribute = sprintf(' data-tc-report-segment="%d"', $segment->getId());
            $jsTemplate = sprintf($jsTemplate, $segmentAttribute);

            $segmentName = htmlspecialchars($segment->getName(), ENT_QUOTES);
            $commentTemplate = sprintf($commentTemplate, $adSlotName, ' - ' . $segmentName);
        } else {
            $jsTemplate = sprintf($jsTemplate, '');
            $commentTemplate = sprintf($commentTemplate, $adSlotName, '');
        }

        return $commentTemplate . $jsTemplate;
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param null|SegmentInterface $segment
     * @return string
     */
    protected function createDisplayAdTagForNativeRonAdSlot(RonAdSlotInterface $ronAdSlot, $segment = null)
    {
        $libraryAdSlot = $ronAdSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
            throw new RuntimeException('expect a LibraryNativeAdSlotInterface object');
        }

        $adSlotName = htmlspecialchars($ronAdSlot->getName(), ENT_QUOTES);
        $publisher = $ronAdSlot->getLibraryAdSlot()->getPublisher();
        $publisherUuid = $publisher->getUuid();

        $commentTemplate = "<!-- %s%s -->\n";
        $jsTemplate = '<script type="text/javascript" src="%s/2.0/adtag.js" data-tc-ron-slot="%d" data-tc-slot-type="native"%s%s></script>' . "\n";

        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $ronAdSlot->getId(), $uuidAttribute, '%s');
        } else {
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $ronAdSlot->getId(), '', '%s');
        }

        if ($segment instanceof SegmentInterface) {
            $segmentName = htmlspecialchars($segment->getName(), ENT_QUOTES);
            $commentTemplate = sprintf($commentTemplate, $adSlotName, ' - ' . $segmentName);

            $segmentAttribute = sprintf(' data-tc-report-segment="%d"', $segment->getId());
            $jsTemplate = sprintf($jsTemplate, $segmentAttribute);
        } else {
            $commentTemplate = sprintf($commentTemplate, $adSlotName, '');
            $jsTemplate = sprintf($jsTemplate, '');
        }

        return $commentTemplate . $jsTemplate;
    }

    /**
     * @param RonAdSlotInterface $adSlot
     * @param null|SegmentInterface $segment
     * @return string
     */
    protected function createDisplayAdTagForDynamicRonAdSlot(RonAdSlotInterface $adSlot, $segment = null)
    {
        $libraryAdSlot = $adSlot->getLibraryAdSlot();
        if (!$libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
            throw new RuntimeException('expect a LibraryDynamicAdSlotInterface object');
        }

        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);
        $publisher = $adSlot->getLibraryAdSlot()->getPublisher();
        $publisherUuid = $publisher->getUuid();

        $commentTemplate = "<!-- %s%s -->\n";
        $jsTemplate = '<script type="text/javascript" src="%s/2.0/adtag.js" data-tc-ron-slot="%d"%s%s%s></script>' . "\n";

        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $adSlot->getId(), $uuidAttribute, '%s', '%s');
        } else {
            $jsTemplate = sprintf($jsTemplate, $this->getBaseTagUrlForPublisher($publisher), $adSlot->getId(), '', '%s', '%s');
        }

        if ($libraryAdSlot->isSupportedNative()) {
            $jsTemplate = sprintf($jsTemplate, ' data-tc-slot-type="native"', '%s');
        } else {
            $jsTemplate = sprintf($jsTemplate, '', '%s');
        }

        if ($segment instanceof SegmentInterface) {
            $segmentName = htmlspecialchars($segment->getName(), ENT_QUOTES);
            $commentTemplate = sprintf($commentTemplate, $adSlotName, ' - ' . $segmentName);

            $segmentAttribute = sprintf(' data-tc-report-segment="%d"', $segment->getId());
            $jsTemplate = sprintf($jsTemplate, $segmentAttribute);
        } else {
            $commentTemplate = sprintf($commentTemplate, $adSlotName, '');
            $jsTemplate = sprintf($jsTemplate, '');
        }

        return $commentTemplate . $jsTemplate;
    }

    /**
     * @param DisplayAdSlotInterface $adSlot
     * @return string
     */
    private function createDisplayAdTagForAdSlot(DisplayAdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);
        $site = $adSlot->getSite();
        $publisherUuid = $site->getPublisher()->getUuid();
        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $site->getDomain());
        $template = '<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d" data-tc-size="%dx%d"%s></script>' . "\n";

        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $adSlot->getId(), $adSlot->getWidth(), $adSlot->getHeight(), $uuidAttribute);
        } else {
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $adSlot->getId(), $adSlot->getWidth(), $adSlot->getHeight(), '');
        }

        return $tag . $template;
    }

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @return string
     */
    private function createDisplayAdTagForDynamicAdSlot(DynamicAdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);
        $site = $adSlot->getSite();
        $publisherUuid = $site->getPublisher()->getUuid();
        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $site->getDomain());
        $template = '<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d"%s%s></script>' . "\n";
        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $adSlot->getId(), $uuidAttribute, '%s');
        } else {
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $adSlot->getId(), '', '%s');
        }

        if ($adSlot->isSupportedNative()) {
            $template = sprintf($template, ' data-tc-slot-type="native"');
        } else {
            $template = sprintf($template, '');
        }

        return $tag . $template;
    }

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @return string
     */
    private function createDisplayAdTagForNativeAdSlot(NativeAdSlotInterface $nativeAdSlot)
    {
        $adSlotName = htmlspecialchars($nativeAdSlot->getName(), ENT_QUOTES);
        $site = $nativeAdSlot->getSite();
        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $site->getDomain());
        $template = '<script type="text/javascript" src="%s/2.0/%d/adtag.js" data-tc-slot="%d" data-tc-slot-type="native"%s></script>' . "\n";
        $publisherUuid = $site->getPublisher()->getUuid();
        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $nativeAdSlot->getId(), $uuidAttribute);
        } else {
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($site->getPublisher()), $site->getId(), $nativeAdSlot->getId(), '');
        }

        return $tag . $template;
    }

    /**
     * @param PublisherInterface $publisher
     * @return string
     */
    public function createDisplayPassbackTag(PublisherInterface $publisher)
    {
        $tag = "<!-- Tagcade Universal Passback -->\n";
        $template = '<script type="text/javascript" src="%s/2.0/adtag.js" data-tc-passback="true"%s></script>' . "\n";
        $publisherUuid = $publisher->getUuid();
        if ($publisherUuid !== null) {
            $uuidAttribute = sprintf(' data-tc-publisher="%s"', $publisherUuid);
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($publisher), $uuidAttribute);
        } else {
            $template = sprintf($template, $this->getBaseTagUrlForPublisher($publisher), '');
        }

        return $tag . $template;
    }

    protected function getBaseTagUrlForPublisher(PublisherInterface $publisher)
    {
        $tagDomain = $publisher->getTagDomain();
        if ($tagDomain === null || empty($tagDomain)) {
            return $this->defaultTagUrl[self::PARAMETER_SECURE] ? self::HTTPS_PROTOCOL . $this->defaultTagUrl[self::PARAMETER_DOMAIN] : self::HTTP_PROTOCOL . $this->defaultTagUrl[self::PARAMETER_DOMAIN];
        }

        if (!isset($tagDomain[self::PARAMETER_SECURE]) || false === $tagDomain[self::PARAMETER_SECURE]) {
            return self::HTTP_PROTOCOL . $tagDomain[self::PARAMETER_DOMAIN];
        }

        return self::HTTPS_PROTOCOL . $tagDomain[self::PARAMETER_DOMAIN];
    }

    /**
     * get Tags For AdSlots
     *
     * @param BaseAdSlotInterface[] $adSlots
     * @return array format as:
     * [
     *      'display' => [
     *          'ad_slots' => [
     *              adSlotId => [
     *                  jstag => <js tags>,
     *                  name => <name of ad slot>,
     *                  site => <domain of own site>
     *              ]
     *          ],
     *      ],
     *      'native' => [
     *          'ad_slots' => [],
     *      ],
     *      'dynamic' => [
     *          'ad_slots' => [],
     *      ]
     * ]
     * return empty if no jstags created
     */
    private function getTagsForAdSlots(array $adSlots)
    {
        // filter all adSlots have publisher that has display module enabled
        $filteredAdSlots = array_filter($adSlots, function ($adSlot) {
            return $adSlot instanceof BaseAdSlotInterface && $adSlot->getSite()->getPublisher()->hasDisplayModule();
        });

        if (count($filteredAdSlots) < 1) {
            return [];
        }

        /*
         * init structure of output js tags, full for 3 keys 'display', 'native' and 'dynamic',
         * and need be removed if empty!!!
         */
        $tags = [
            'display' => [
                'ad_slots' => [],
            ],

            'native' => [
                'ad_slots' => [],
            ],

            'dynamic' => [
                'ad_slots' => [],
            ]
        ];

        // create js tags
        foreach ($filteredAdSlots as $adSlot) {
            /** @var BaseAdSlotInterface $adSlot */
            if (!array_key_exists($adSlot->getType(), $tags)) {
                continue; // not support generating tags for this ad slot type
            }

            $adSlots = &$tags[$adSlot->getType()]['ad_slots'];

            $adSlots[$adSlot->getId()] = array(
                'jstag' => $this->createJsTags($adSlot),
                'name' => $adSlot->getName(),
                'site' => $adSlot->getSite()->getDomain()
            );
        }

        // remove the elements if their 'ad_slots' element is empty
        $removeKeys = [];

        array_walk($tags,
            function ($tagItem, $key) use (&$removeKeys) {
                if (is_array($tagItem) && count($tagItem['ad_slots']) < 1) {
                    $removeKeys[] = $key;
                }
            }
        );

        array_walk($removeKeys,
            function ($key) use (&$tags) {
                unset($tags[$key]);
            }
        );

        return $tags;
    }
} 