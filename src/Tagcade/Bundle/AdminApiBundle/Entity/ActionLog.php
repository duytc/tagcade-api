<?php

namespace Tagcade\Bundle\AdminApiBundle\Entity;

use Tagcade\Model\User\UserEntityInterface;

use Doctrine\ORM\Mapping as ORM;

class ActionLog
{
    protected $id;

    protected $user;

    protected $ip;

    protected $serverIp;

    protected $action;

    protected $data;

    protected $createdAt;


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|UserEntityInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserEntityInterface $user
     * @return $this
     */
    public function setUser(UserEntityInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return $this
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getServerIp()
    {
        return $this->serverIp;
    }

    /**
     * @param string $serverIp
     * @return $this
     */
    public function setServerIp($serverIp)
    {
        $this->serverIp = $serverIp;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}