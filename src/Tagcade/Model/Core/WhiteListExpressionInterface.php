<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

interface WhiteListExpressionInterface extends ModelInterface
{
    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return DisplayWhiteListInterface
     */
    public function getWhiteList();

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return self
     */
    public function setWhiteList($whiteList);

    /**
     * @return LibraryExpressionInterface
     */
    public function getLibraryExpression();

    /**
     * @param LibraryExpressionInterface $libraryExpression
     * @return self
     */
    public function setLibraryExpression($libraryExpression);

    /**
     * @return LibraryAdTagInterface
     */
    public function getLibraryAdTag();

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return self
     */
    public function setLibraryAdTag($libraryAdTag);
}