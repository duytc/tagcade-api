<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BlacklistInterface extends ModelInterface
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
     * @return string
     */
    public function getSuffixKey();

    /**
     * @param string $suffixKey
     * @return self
     */
    public function setSuffixKey($suffixKey);

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
     * @return PublisherInterface
     */
    public function getPublisher();

    /**
     * @return null|int
     */
    public function getPublisherId();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher($publisher);
}
