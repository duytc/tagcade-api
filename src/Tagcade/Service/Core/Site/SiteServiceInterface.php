<?php


namespace Tagcade\Service\Core\Site;


use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SiteServiceInterface
{
    public function getSubPublisherFromDomain(AdNetworkPartnerInterface $adNetworkPartner, PublisherInterface $publisher, $domain);
}