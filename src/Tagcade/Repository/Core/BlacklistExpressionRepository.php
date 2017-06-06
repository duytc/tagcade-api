<?php

namespace Tagcade\Repository\Core;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class BlacklistExpressionRepository extends EntityRepository implements BlacklistExpressionRepositoryInterface
{
    public function checkLibraryExpressionExist(LibraryExpressionInterface $libraryExpression, $blacklistId)
    {
        $result = $this->createQueryBuilder('be')
            ->where('be.blacklist = :blacklist')
            ->andWhere('be.libraryExpression = :libraryExpression')
            ->setParameter('blacklist', $blacklistId, Type::INTEGER)
            ->setParameter('libraryExpression', $libraryExpression)
            ->getQuery()
            ->getResult();

        return count($result) > 0;
    }

    public function checkLibraryAdTagExist(LibraryAdTagInterface $libraryAdTag, $blacklistId)
    {
        $result = $this->createQueryBuilder('be')
            ->where('be.blacklist = :blacklist')
            ->andWhere('be.libraryAdTag = :libraryAdTag')
            ->setParameter('blacklist', $blacklistId)
            ->setParameter('libraryAdTag', $libraryAdTag)
            ->getQuery()
            ->getResult();

        return $result;
    }


    /**
     * @param $blacklist
     * @return array
     */
    public function getByBlackList($blacklist)
    {
        return $this->createQueryBuilder('be')
            ->where('be.blacklist = :blacklist')
            ->setParameter('blacklist', $blacklist->getId(), Type::INTEGER)
            ->getQuery()
            ->getResult();
    }
}