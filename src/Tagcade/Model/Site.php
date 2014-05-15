<?php

namespace Tagcade\Model;

use Tagcade\Model\User\Publisher;
use Tagcade\Model\User\UserInterface;

class Site
{
    protected $id;

    /**
     * @var UserInterface
     */
    protected $publisher;
    protected $name;
    protected $domain;

    public function __construct(Publisher $publisher, $name, $domain)
    {
        $this->setPublisher($publisher);
        $this->name = $name;
        $this->domain = $domain;
    }

    public function setPublisher(Publisher $publisher) {
        $this->publisher = $publisher->getUser();
    }

    public function getPublisherId()
    {
        return $this->publisher->getId();
    }
}
