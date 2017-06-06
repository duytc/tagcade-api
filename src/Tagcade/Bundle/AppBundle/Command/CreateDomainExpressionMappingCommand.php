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

class CreateDomainExpressionMappingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:api:domain-expression-mapping:create')
            ->setDescription('Create domain expression mapping in every expression descriptor (Dynamic Ad Slot and Ad Tag targeting)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $libraryAdTagRepository = $this->getContainer()->get('tagcade.repository.library_ad_tag');
        $libraryAdTags = $libraryAdTagRepository->getLibraryAdTagHasExpressionDescriptor();

        $output->writeln('start updating for standalone ad tag');
        $progress = new ProgressBar($output, count($libraryAdTags));
        $progress->start();

        /** @var LibraryAdTagInterface $libraryAdTag */
        foreach ($libraryAdTags as $libraryAdTag) {
            $progress->advance();

            $descriptor = $libraryAdTag->getExpressionDescriptor();
            $result = $this->updateDomainExpression($descriptor, $em);
            $blacklists = $result['blacklist'];
            if (!empty($blacklists)) {
                foreach ($blacklists as $blacklist) {
                    $blacklistExpression = (new BlacklistExpression())->setLibraryAdTag($libraryAdTag)->setBlacklist($blacklist);
                    $em->persist($blacklistExpression);
                }
            }

            $whiteLists = $result['whitelist'];
            if (!empty($whiteLists)) {
                foreach ($whiteLists as $whiteList) {
                    $whiteListExpression = (new WhiteListExpression())->setLibraryAdTag($libraryAdTag)->setWhiteList($whiteList);
                    $em->persist($whiteListExpression);
                }
            }
        }

        $em->flush();
        $progress->finish();
        $output->writeln('');
        $output->writeln('updating for standalone ad tag done');

        $libraryExpressionRepository = $this->getContainer()->get('tagcade.domain_manager.library_expression');
        $libraryExpressions = $libraryExpressionRepository->all();
        $output->writeln('start updating for library expression');
        $progress = new ProgressBar($output, count($libraryExpressions));
        $progress->start();

        /** @var LibraryExpressionInterface $libraryExpression */
        foreach ($libraryExpressions as $libraryExpression) {
            $progress->advance();

            $descriptor = $libraryExpression->getExpressionDescriptor();
            if (empty($descriptor)) {
                continue;
            }
            $result = $this->updateDomainExpression($descriptor, $em);
            $blacklists = $result['blacklist'];
            if (!empty($blacklists)) {
                foreach ($blacklists as $blacklist) {
                    $blacklistExpression = (new BlacklistExpression())->setLibraryExpression($libraryExpression)->setBlacklist($blacklist);
                    $em->persist($blacklistExpression);
                }
            }

            $whiteLists = $result['whitelist'];
            if (!empty($whiteLists)) {
                foreach ($whiteLists as $whiteList) {
                    $whiteListExpression = (new WhiteListExpression())->setLibraryExpression($libraryExpression)->setWhiteList($whiteList);
                    $em->persist($whiteListExpression);
                }
            }
        }

        $em->flush();
        $progress->finish();
        $output->writeln('');
        $output->writeln('updating for library expression done');
    }

    protected function updateDomainExpression($expression, EntityManagerInterface $em)
    {
        if (array_key_exists(ExpressionInJsGenerator::KEY_GROUP_VAL, $expression)) {
            return $this->updateDomainExpressionForGroupObject($expression, $em);
        }

        return $this->updateDomainExpressionForConditionObject($expression, $em);
    }

    protected function updateDomainExpressionForConditionObject($expression, EntityManagerInterface $em)
    {
        $blacklists = [];
        $whiteLists = [];

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
                    $blacklist = $displayBlacklistRepository->find($id);
                    if ($blacklist instanceof DisplayBlacklistInterface) {
                        $blacklists[] = $blacklist;
                    }
                }
            }

            if (in_array($expression[ExpressionInJsGenerator::KEY_EXPRESSION_CMP], ['inWhitelist', 'notInWhitelist'])) {
                $ids = explode(',', $expression[ExpressionInJsGenerator::KEY_EXPRESSION_VAL]);
                foreach ($ids as $id) {
                    $whiteList = $displayWhiteListRepository->find($id);
                    if ($whiteList instanceof DisplayWhiteListInterface) {
                        $whiteLists[] = $whiteList;
                    }
                }

            }
        }

        return array (
            'blacklist' => $blacklists,
            'whitelist' => $whiteLists
        );
    }

    protected function updateDomainExpressionForGroupObject($expression, EntityManagerInterface $em)
    {
        $blacklists = [];
        $whiteLists = [];
        $groupVal = $expression[ExpressionInJsGenerator::KEY_GROUP_VAL];
        foreach ($groupVal as $descriptor) {
            $result = $this->updateDomainExpression($descriptor, $em);
            $blacklists = array_merge($blacklists, $result['blacklist']);
            $whiteLists = array_merge($whiteLists, $result['whitelist']);
        }

        return array (
            'blacklist' => $blacklists,
            'whitelist' => $whiteLists
        );
    }
}