<?php


namespace Tagcade\Service\Core\AdTag;


use Tagcade\Model\Core\AdNetworkPartnerInterface;
use Tagcade\Model\User\Role\UserRoleInterface;

interface PartnerTagIdFinderInterface
{
    /**
     * @param AdNetworkPartnerInterface $adNetworkPartner
     * @param UserRoleInterface $publisher
     * @param $partnerTagId
     * @return mixed
     */
    public function getTcTag(AdNetworkPartnerInterface $adNetworkPartner, UserRoleInterface $publisher, $partnerTagId);
}