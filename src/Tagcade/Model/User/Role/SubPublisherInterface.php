<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\Core\SiteInterface;

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
     * @return array|SiteInterface[] $sites
     */
    public function getSites();

    /**
     * @param array|SiteInterface[] $sites
     * @return self
     */
    public function setSites(array $sites);
}