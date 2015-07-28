<?php

namespace Tagcade\Domain\DTO\Core;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;

class SiteStatus
{
    /**
     * @var SiteInterface
     */
    private $site;

    private $activeAdTagsCount;

    private $pausedAdTagsCount;

    function __construct(SiteInterface $site, AdNetworkInterface $adNetwork)
    {
        $this->site = $site;

        $adTags = $adNetwork->getAdTags();
        $this->pausedAdTagsCount = count(
            array_filter(
                $adTags,
                function(AdTagInterface $adTag) use ($site)
                {
                    return $adTag->getAdSlot()->getSite()->getId() === $site->getId() && $adTag->isActive() === false;
                }
            )
        );

        $this->activeAdTagsCount = count(
            array_filter(
                $adTags,
                function(AdTagInterface $adTag) use ($site)
                {
                    return $adTag->getAdSlot()->getSite()->getId() === $site->getId() && $adTag->isActive() === true;
                }
            )
        );

    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return mixed
     */
    public function getActiveAdTagsCount()
    {
        return $this->activeAdTagsCount;
    }

    /**
     * @return mixed
     */
    public function getPausedAdTagsCount()
    {
        return $this->pausedAdTagsCount;
    }
}