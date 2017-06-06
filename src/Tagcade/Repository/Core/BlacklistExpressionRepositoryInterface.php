<?php

namespace Tagcade\Repository\Core;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

interface BlacklistExpressionRepositoryInterface extends ObjectRepository
{
    public function checkLibraryExpressionExist(LibraryExpressionInterface $libraryExpression, $blacklistId);

    public function checkLibraryAdTagExist(LibraryAdTagInterface $libraryAdTag, $blacklistId);

    public function getByBlackList($blacklist);
}