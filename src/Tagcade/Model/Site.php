<?php

namespace Tagcade\Model;

use Tagcade\Model\User\Role\Publisher;
use Tagcade\Model\User\UserEntityInterface;

class Site implements SiteInterface
{
    protected $id;

    /**
     * @var UserEntityInterface
     */
    protected $publisher;
    protected $name;
    protected $domain;

    public function __construct($name, $domain, Publisher $publisher = null)
    {
        $this->name = $name;
        $this->domain = $domain;

        if ($publisher){
            $this->setPublisher($publisher);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function setPublisher(Publisher $publisher) {
        $this->publisher = $publisher->getUser();
    }

    public function getPublisherId()
    {
        if (!$this->publisher) {
            return null;
        }

        return $this->publisher->getId();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
}
