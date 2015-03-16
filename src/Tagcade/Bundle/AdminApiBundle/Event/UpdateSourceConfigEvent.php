<?php

namespace Tagcade\Bundle\AdminApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Tagcade\Model\Core\SiteInterface;

class UpdateSourceConfigEvent extends Event
{
    const NAME = 'tagcade_admin_api.event.update_source_config';

    /**
     * @var SiteInterface
     */
    protected $site;

    function __construct($site)
    {
        $this->site = $site;
    }

    /**
     * @return SiteInterface
     */
    public function getSite()
    {
        return $this->site;
    }
}