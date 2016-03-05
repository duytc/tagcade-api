<?php

namespace Tagcade\Model\User\Role;

use Tagcade\Model\Core\SubPublisherSiteInterface;

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
     * @return array|SubPublisherSiteInterface[]
     */
    public function getSubPublisherSites();

    /**
     * @param array|SubPublisherSiteInterface[] $subPublisherSites
     * @return self
     */
    public function setSubPublisherSites(array $subPublisherSites);

    /**
     * @return array
     */
    public function getEnabledModules();
}