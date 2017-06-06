<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\Entity\Core\BlacklistExpression;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Entity\Core\DisplayWhiteList;
use Tagcade\Entity\Core\WhiteListExpression;
use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class VerifyEmptyExpressionConditionValueCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:api:expression-condition-value:verify')
            ->setDescription('Remove expression descriptor (Dynamic Ad Slot and Ad Tag targeting) which has empty value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $libraryAdTagRepository = $this->getContainer()->get('tagcade.repository.library_ad_tag');
        $libraryAdTags = $libraryAdTagRepository->getLibraryAdTagHasExpressionDescriptor();

        $output->writeln('start updating for standalone ad tag');

        /** @var LibraryAdTagInterface $libraryAdTag */
        foreach ($libraryAdTags as $libraryAdTag) {
            $descriptor = $libraryAdTag->getExpressionDescriptor();
            $result = $this->updateDomainExpression($descriptor, $em);
            if ($result === true) {
                $output->writeln(sprintf('<error>the missing library ad tag is %d</error>', $libraryAdTag->getId()));
            }
        }

        $output->writeln('updating for standalone ad tag done');

        $libraryExpressionRepository = $this->getContainer()->get('tagcade.domain_manager.library_expression');
        $libraryExpressions = $libraryExpressionRepository->all();
        $output->writeln('start updating for library expression');
        /** @var LibraryExpressionInterface $libraryExpression */
        foreach ($libraryExpressions as $libraryExpression) {

            $descriptor = $libraryExpression->getExpressionDescriptor();
            if (empty($descriptor)) {
                continue;
            }
            $result = $this->updateDomainExpression($descriptor, $em);
            if ($result === true) {
                $output->writeln(sprintf('<error>the missing library expression is %d</error>', $libraryExpression->getId()));
            }
        }

        $output->writeln('');
        $output->writeln('updating for library expression done');
    }

    protected function updateDomainExpression($expression, EntityManagerInterface $em)
    {
        if (array_key_exists(ExpressionInJsGenerator::KEY_GROUP_VAL, $expression)) {
            return $this->updateDomainExpressionForGroupObject($expression, $em);
        }

        return $this->updateDomainExpressionForConditionObject($expression);
    }

    protected function updateDomainExpressionForConditionObject($expression)
    {
        if (
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAR, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_CMP, $expression) &&
            array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAL, $expression)
        ) {
            if (empty($expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL])) {
                return true;
            }
        }

        return false;
    }

    protected function updateDomainExpressionForGroupObject($expression, EntityManagerInterface $em)
    {
        $groupVal = $expression[ExpressionInJsGenerator::KEY_GROUP_VAL];
        foreach ($groupVal as $descriptor) {
            $result = $this->updateDomainExpression($descriptor, $em);
            if ($result === true) {
                return $result;
            }
        }

        return false;
    }
}