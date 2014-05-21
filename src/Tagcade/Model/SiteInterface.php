<?php

namespace Tagcade\Model;

use Tagcade\Model\User\Role\PublisherInterface;

interface SiteInterface
{
    public function getId();
    public function setName($name);
    public function getName();
    public function setDomain($domain);
    public function getDomain();
    public function setPublisher(PublisherInterface $publisher);
}
