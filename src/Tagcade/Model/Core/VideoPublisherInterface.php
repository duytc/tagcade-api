<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;

interface VideoPublisherInterface extends ModelInterface
{
    /**
     * @return UserEntityInterface
     */
    public function getPublisher();

    /**
     * @param UserEntityInterface $publisher
     * @return self
     */
    public function setPublisher(UserEntityInterface $publisher);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);
}
