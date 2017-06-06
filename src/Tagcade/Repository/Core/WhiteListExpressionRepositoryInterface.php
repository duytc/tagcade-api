<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

interface WhiteListExpressionRepositoryInterface extends ObjectRepository
{
    public function checkLibraryExpressionExist(LibraryExpressionInterface $libraryExpression, $whiteListId);

    public function checkLibraryAdTagExist(LibraryAdTagInterface $libraryAdTag, $whiteListId);

    public function getByWhiteList(DisplayWhiteListInterface $whiteList);
}