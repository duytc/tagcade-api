<?php

namespace Tagcade\Model\Core;

use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\StringUtilTrait;

class Blacklist implements BlacklistInterface
{
    use StringUtilTrait;
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var UserRoleInterface
     */
    protected $publisher;

    /**
     * @var array
     */
    protected $domains;

    /**
     * @var string
     */
    protected $suffixKey;


    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getSuffixKey()
    {
        return $this->suffixKey;
    }

    /**
     * @param string $suffixKey
     * @return self
     */
    public function setSuffixKey($suffixKey)
    {
        $this->suffixKey = $suffixKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }

    /**
     * @param array $domains
     * @return self
     */
    public function setDomains($domains)
    {
        $this->domains = $domains;
        return $this;
    }

    /**
     * @return UserRoleInterface
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    public function getPublisherId()
    {
        if ($this->publisher instanceof UserRoleInterface) {
            return $this->publisher->getId();
        }

        return null;
    }

    /**
     * @param UserRoleInterface $publisher
     * @return self
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function __toString()
    {
        return $this->id . '-' . $this->getName();
    }
}
