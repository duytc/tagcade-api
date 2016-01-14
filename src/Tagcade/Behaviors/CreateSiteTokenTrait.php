<?php

namespace Tagcade\Behaviors;


trait CreateSiteTokenTrait
{
    /**
     * Create site token from domain and publisher
     *
     * @param $publisherId
     * @param $domain
     * @return string
     */
    protected function createSiteHash($publisherId, $domain)
    {
        return md5(sprintf('%d%s', $publisherId, $domain));
    }
} 