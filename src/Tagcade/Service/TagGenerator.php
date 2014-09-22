<?php

namespace Tagcade\Service;

use Tagcade\Model\Core\AdSlotInterface;
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
            $tags['display'] = [
                'passback' => $this->createDisplayPassbackTag($site),
                'ad_slots' => [],
            ];

            $adSlots = &$tags['display']['ad_slots'];

            foreach($site->getAdSlots() as $adSlot) {
                /** @var AdSlotInterface $adSlot */
                $adSlots[$adSlot->getName()] = $this->createDisplayAdTag($adSlot);
            }
        }

        return $tags;
    }

    /**
     * @param SiteInterface $site
     * @return string
     */
    public function createHeaderTag(SiteInterface $site)
    {
        $tag = sprintf('<script type="text/javascript" src="%s/%d/tagcade.js"></script>' . "\n", $this->baseTagUrl, $site->getId());

        return $tag;
    }

    /**
     * @param AdSlotInterface $adSlot
     * @return string
     */
    public function createDisplayAdTag(AdSlotInterface $adSlot)
    {
        $adSlotName = htmlspecialchars($adSlot->getName(), ENT_QUOTES);

        $tag = sprintf("<!-- %s - %s -->\n", $adSlotName, $adSlot->getSite()->getDomain());
        $tag .= '<script type="text/javascript">' . "\n";
        $tag .= sprintf("var tc_slot = %d;\n", $adSlot->getId());
        $tag .= sprintf("var tc_size = '%dx%d';\n", $adSlot->getWidth(), $adSlot->getHeight());
        $tag .= "</script>\n";
        $tag .= sprintf('<script type="text/javascript" src="%s/adtag.js"></script>' . "\n", $this->baseTagUrl);

        return $tag;
    }

    /**
     * @param SiteInterface $site
     * @return string
     */
    public function createDisplayPassbackTag(SiteInterface $site)
    {
        $tag = sprintf('<script type="text/javascript" src="%s/%d/passback.js"></script>' . "\n", $this->baseTagUrl, $site->getId());

        return $tag;
    }
} 