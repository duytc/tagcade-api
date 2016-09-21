<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;

interface VideoDemandPartnerInterface extends ModelInterface
{
    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getNameCanonical();

    /**
     * @return mixed
     */
    public function getDefaultTagURL();

    /**
     * @param mixed $defaultTagURL
     * @return self
     */
    public function setDefaultTagURL($defaultTagURL);

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
     * @return self
     */
    public function increasePausedAdTagsCount();

    /**
     * @return self
     */
    public function decreasePausedAdTagsCount();

    /**
     * @return self
     */
    public function increaseActiveAdTagsCount();

    /**
     * @return self
     */
    public function decreaseActiveAdTagsCount();

    /**
     * @return mixed
     */
    public function getVideoDemandAdTags();

    /**
     * @return mixed
     */
    public function getLibraryVideoDemandAdTags();

    /**
     * @return mixed
     */
    public function getActiveAdTagsCount();

    /**
     * @param mixed $activeAdTagsCount
     * @return self
     */
    public function setActiveAdTagsCount($activeAdTagsCount);

    /**
     * @return mixed
     */
    public function getPausedAdTagsCount();

    /**
     * @param mixed $pausedAdTagsCount
     * @return self
     */
    public function setPausedAdTagsCount($pausedAdTagsCount);
} 