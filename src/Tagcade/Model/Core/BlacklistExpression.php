<?php


namespace Tagcade\Model\Core;


class BlacklistExpression implements BlacklistExpressionInterface
{
    protected $id;

    /**
     * @var DisplayBlacklistInterface
     */
    protected $blacklist;

    /**
     * @var LibraryExpressionInterface
     */
    protected $libraryExpression;

    /**
     * @var LibraryAdTagInterface
     */
    protected $libraryAdTag;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DisplayBlacklistInterface
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return self
     */
    public function setBlacklist($blacklist)
    {
        $this->blacklist = $blacklist;
        return $this;
    }

    /**
     * @return LibraryExpressionInterface
     */
    public function getLibraryExpression()
    {
        return $this->libraryExpression;
    }

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return self
     */
    public function setLibraryExpression($libraryExpression)
    {
        $this->libraryExpression = $libraryExpression;
        return $this;
    }

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag()
    {
        return $this->libraryAdTag;
    }

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return self
     */
    public function setLibraryAdTag($libraryAdTag)
    {
        $this->libraryAdTag = $libraryAdTag;
        return $this;
    }
}