<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\User\UserEntityInterface;

class VideoPublisher implements VideoPublisherInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var UserEntityInterface $publisher
     */
    protected $publisher;


    /**
     * @inheritdoc
     */
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
     * @inheritdoc
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @inheritdoc
     */
    public function setPublisher(UserEntityInterface $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }
}