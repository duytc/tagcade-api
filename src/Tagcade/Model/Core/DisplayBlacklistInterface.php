<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayBlacklistInterface extends ModelInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getDomains();

    /**
     * @param array $domains
     * @return self
     */
    public function setDomains($domains);

    /**
     * @return NetworkBlacklistInterface
     */
    public function getNetworkBlacklists();

    /**
     * @param NetworkBlacklistInterface $networkBlacklists
     */
    public function setNetworkBlacklists($networkBlacklists);

    public function addNetworkBlacklist(NetworkBlacklistInterface $networkBlacklist);

    /**
     * @return array
     */
    public function getAdNetworks();

    /**
     * @return boolean
     */
    public function isDefault();

    /**
     * @param boolean $default
     */
    public function setIsDefault($default);

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     */
    public function setPublisher($publisher);
}
