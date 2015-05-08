<?php

namespace Tagcade\Bundle\AdminApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class NewSourceConfigEvent extends Event
{
    const NAME = 'tagcade_admin_api.event.new_source_config';

    /**
     * @var array
     */
    protected $emails;
    /**
     * @var array
     */
    protected $sites;

    function __construct(array $emails, array $sites)
    {
        $this->emails = $emails;
        $this->sites = $sites;
    }

    /**
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @return array
     */
    public function getSites()
    {
        return $this->sites;
    }
}