<?php

namespace Tagcade\Domain\DTO\Core;


use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

class SiteStatus
{
    /**
     * @var SiteInterface
     */
    private $site;

    private $active;

    function __construct(SiteInterface $site, $active)
    {
        $this->site = $site;
        $this->active = $active;
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
    public function getActive()
    {
        return $this->active;
    }

}