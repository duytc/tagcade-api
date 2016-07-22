<?php

namespace Tagcade\Domain\DTO\Core;


use Tagcade\Model\Core\SiteInterface;

class SiteStatus
{
    /**
     * @var SiteInterface
     */
    private $site;

    private $activeAdTagsCount;

    private $pausedAdTagsCount;

    function __construct(SiteInterface $site, $activeAdTagsCount, $pausedAdTagsCount)
    {
        $this->site = $site;
        $this->pausedAdTagsCount = $pausedAdTagsCount;
        $this->activeAdTagsCount = $activeAdTagsCount;
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