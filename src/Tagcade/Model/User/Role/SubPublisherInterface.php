<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Core\SubPublisherPartnerRevenueInterface;

interface SubPublisherInterface extends UserRoleInterface
{
    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return array
     */
    public function getEnabledModules();

    /**
     * @return boolean
     */
    public function isDemandSourceTransparency();

    /**
     * @param boolean $demandSourceTransparency
     * @return self
     */
    public function setDemandSourceTransparency($demandSourceTransparency);

    /**
     * @return boolean
     */
    public function isEnableViewTagcadeReport();

    /**
     * @param boolean $enableViewTagcadeReport
     * @return self
     */
    public function setEnableViewTagcadeReport($enableViewTagcadeReport);

    /**
     * @return array|SubPublisherPartnerRevenueInterface[]
     */
    public function getSubPublisherPartnerRevenue();

    /**
     * @param array|SubPublisherPartnerRevenueInterface[] $subPublisherPartnerRevenue
     * @return self
     */
    public function setSubPublisherPartnerRevenue($subPublisherPartnerRevenue);

    /**
     * @return array|SiteInterface[] $sites
     */
    public function getSites();

    /**
     * @param array|SiteInterface[] $sites
     * @return self
     */
    public function setSites(array $sites);
}