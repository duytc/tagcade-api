<?php


namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

interface BlacklistExpressionInterface extends ModelInterface
{
    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return DisplayBlacklistInterface
     */
    public function getBlacklist();

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @return self
     */
    public function setBlacklist($blacklist);

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