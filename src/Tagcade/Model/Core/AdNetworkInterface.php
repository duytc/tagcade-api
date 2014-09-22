<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdNetworkInterface extends ModelInterface
{
    /**
     * @return UserEntityInterface|null
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();

    /**
     * @param PublisherInterface $publisher
     * @return self
     */
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getUrl();

    /**
     * @param string $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param $boolean
     * @return $this
     */
    public function setActive($boolean);
}