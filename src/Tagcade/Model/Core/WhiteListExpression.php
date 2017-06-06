<?php


namespace Tagcade\Model\Core;


class WhiteListExpression implements WhiteListExpressionInterface
{
    protected $id;

    /**
     * @var DisplayWhiteListInterface
     */
    protected $whiteList;

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
     * @return DisplayWhiteListInterface
     */
    public function getWhiteList()
    {
        return $this->whiteList;
    }

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return self
     */
    public function setWhiteList($whiteList)
    {
        $this->whiteList = $whiteList;
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