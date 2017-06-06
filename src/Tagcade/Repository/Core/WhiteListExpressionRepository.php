<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\WhiteListInterface;

class WhiteListExpressionRepository extends EntityRepository implements WhiteListExpressionRepositoryInterface
{
    public function checkLibraryExpressionExist(LibraryExpressionInterface $libraryExpression, $whiteListId)
    {
        $result = $this->createQueryBuilder('we')
            ->where('we.whiteList = :whiteList')
            ->andWhere('we.libraryExpression = :libraryExpression')
            ->setParameter('whiteList', $whiteListId, Type::INTEGER)
            ->setParameter('libraryExpression', $libraryExpression)
            ->getQuery()
            ->getResult();

        return count($result) > 0;
    }

    public function checkLibraryAdTagExist(LibraryAdTagInterface $libraryAdTag, $whiteListId)
    {
        $result = $this->createQueryBuilder('we')
            ->where('we.whiteList = :whiteList')
            ->andWhere('we.libraryAdTag = :libraryAdTag')
            ->setParameter('whiteList', $whiteListId)
            ->setParameter('libraryAdTag', $libraryAdTag)
            ->getQuery()
            ->getResult();

        return count($result) > 0;
    }

    /**
     * @param DisplayWhiteListInterface $whiteList
     * @return array
     */
    public function getByWhiteList(DisplayWhiteListInterface $whiteList)
    {
        return $this->createQueryBuilder('we')
            ->where('we.whiteList = :whiteList')
            ->setParameter('whiteList', $whiteList->getId(), Type::INTEGER)
            ->getQuery()
            ->getResult();
    }
}