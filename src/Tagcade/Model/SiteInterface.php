<?php

namespace Tagcade\Model;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

interface SiteInterface
{
    public function getId();
    public function setName($name);
    public function getName();
    public function setDomain($domain);
    public function getDomain();
    public function setPublisher(PublisherInterface $publisher);

    /**
     * @return UserEntityInterface|null
     */
    public function getPublisher();

    /**
     * @return int|null
     */
    public function getPublisherId();
}
