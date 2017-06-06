<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Entity\Core\BlacklistExpression;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Entity\Core\DisplayWhiteList;
use Tagcade\Entity\Core\WhiteListExpression;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;

class LibraryAdTagExpressionDescriptorChangeListener
{
    protected $changedEntities = [];
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof LibraryAdTagInterface) {
            return;
        }

        $this->createDomainMappingForLibraryAdTag($entity, $args->getEntityManager());
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof LibraryAdTagInterface) {
            return;
        }

        if (!$args->hasChangedField('expressionDescriptor')) {
            return;
        }

        $this->createDomainMappingForLibraryAdTag($entity, $args->getEntityManager());
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->changedEntities)) {
            $em = $args->getEntityManager();
            foreach ($this->changedEntities as $entity) {
                $em->merge($entity);
            }

            $this->changedEntities = []; // reset updated expressions

            $em->flush();
        }
    }

    protected function createDomainMappingForLibraryAdTag(LibraryAdTagInterface $libraryAdTag, EntityManagerInterface $em)
    {
        $descriptor = $libraryAdTag->getExpressionDescriptor();
        $this->createDomainMappingForDescriptor($descriptor, $libraryAdTag, $em);
    }

    protected function createDomainMappingForDescriptor($descriptor, LibraryAdTagInterface $libraryAdTag, EntityManagerInterface $em)
    {
        if (array_key_exists(ExpressionInJsGenerator::KEY_GROUP_VAL, $descriptor)) {
            $this->createDomainMappingForGroupObject($descriptor, $libraryAdTag, $em);
        } else {
            $this->createDomainMappingForConditionObject($descriptor, $libraryAdTag, $em);
        }
    }

    protected function createDomainMappingForGroupObject($descriptor, LibraryAdTagInterface $libraryAdTag, EntityManagerInterface $em)
    {
        foreach ($descriptor[ExpressionInJsGenerator::KEY_GROUP_VAL] as $expression) {
            $this->createDomainMappingForDescriptor($expression, $libraryAdTag, $em);
        }
    }

    protected function createDomainMappingForConditionObject($expression, LibraryAdTagInterface $libraryAdTag, EntityManagerInterface $em)
    {
        $blacklistExpressionRepository = $em->getRepository(BlacklistExpression::class);
        $whiteListExpressionRepository= $em->getRepository(WhiteListExpression::class);
        $displayBlacklistRepository = $em->getRepository(DisplayBlacklist::class);
        $displayWhiteListRepository = $em->getRepository(DisplayWhiteList::class);

        if (
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAR, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_CMP, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAL, $expression) &&
            $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAR] == '${DOMAIN}'
        ) {
            if (in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inBlacklist', 'notInBlacklist'])) {
                $ids = explode(',', $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                foreach ($ids as $id) {
                    $id = intval($id);
                    $blacklist = $displayBlacklistRepository->find($id);
                    if (!$blacklist instanceof DisplayBlacklistInterface) {
                        continue;
                    }

                    if (!$blacklistExpressionRepository->checkLibraryAdTagExist($libraryAdTag, $blacklist)) {
                        $blacklistExpression = (new BlacklistExpression())->setBlacklist($blacklist)->setLibraryAdTag($libraryAdTag);
                        $em->persist($blacklistExpression);
                        $this->changedEntities[] = $blacklistExpression;
                    }
                }
            }

            if (in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inWhitelist', 'notInWhitelist'])) {
                $ids = explode(',', $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                foreach ($ids as $id) {
                    $id = intval($id);
                    $whiteList = $displayWhiteListRepository->find($id);
                    if (!$whiteList instanceof DisplayWhiteListInterface) {
                        continue;
                    }

                    if (!$whiteListExpressionRepository->checkLibraryAdTagExist($libraryAdTag, $id)) {
                        $whiteListExpression = (new WhiteListExpression())->setWhiteList($whiteList)->setLibraryAdTag($libraryAdTag);
                        $em->persist($whiteListExpression);
                        $this->changedEntities[] = $whiteListExpression;
                    }
                }
            }
        }
    }
}