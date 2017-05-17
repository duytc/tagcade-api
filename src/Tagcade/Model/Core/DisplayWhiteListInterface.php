<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DisplayWhiteListInterface extends ModelInterface
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
     * @return NetworkWhiteListInterface
     */
    public function getNetworkWhiteLists();

    /**
     * @param NetworkWhiteListInterface[] $networkWhiteLists
     */
    public function setNetworkWhiteLists($networkWhiteLists);

    public function addNetworkWhiteList(NetworkWhiteListInterface $networkWhiteList);

    /**
     * @return array
     */
    public function getAdNetworks();

    /**
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @param PublisherInterface $publisher
     */
    public function setPublisher($publisher);
}
