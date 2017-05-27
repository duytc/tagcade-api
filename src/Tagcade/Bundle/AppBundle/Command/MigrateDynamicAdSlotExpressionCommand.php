<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tagcade\Bundle\ApiBundle\Service\ExpressionInJsGenerator;
use Tagcade\DomainManager\LibraryDynamicAdSlotManagerInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;

class MigrateDynamicAdSlotExpressionCommand extends ContainerAwareCommand
{
    const VERSION_REMOVE_DOMAIN_VAR_IN_EXPRESSION = 1;

    private static $currentVersion = self::VERSION_REMOVE_DOMAIN_VAR_IN_EXPRESSION;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LibraryDynamicAdSlotManagerInterface
     */
    private $libraryDynamicAdSlotManager;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('tc:migration:dynamic-ad-slot:expression:remove-domain')
            ->setDescription('Remove all expression related to ${DOMAIN} var. This is because of now ${DOMAIN} only
            support comparators such as in/notIn BlackList/Whitelist');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();
        $this->logger = $container->get('logger');
        $this->libraryDynamicAdSlotManager = $container->get('tagcade.domain_manager.library_dynamic_ad_slot');

        /* only need migrate expression descriptors of library dynamic ad slots because dynamic ad slot used that libs */
        $libraryDynamicAdSlots = $this->libraryDynamicAdSlotManager->all();

        $output->writeln(sprintf('migrating expression descriptors of %d library dynamic ad slots to latest format...', count($libraryDynamicAdSlots)));

        // migrate
        $migratedLibraryDynamicAdSlotCount = 0;

        switch (self::$currentVersion) {
            case self::VERSION_REMOVE_DOMAIN_VAR_IN_EXPRESSION:
                $this->migrateExpressionsForLibraryDynamicAdSlot($libraryDynamicAdSlots, $migratedLibraryDynamicAdSlotCount, $output);

                break;
        }

        $output->writeln(sprintf('command run successfully: expression descriptors of %d library dynamic ad slot updated.', $migratedLibraryDynamicAdSlotCount));
    }

    /**
     * @param array|LibraryDynamicAdSlotInterface[] $libraryDynamicAdSlots
     * @param int $migratedLibraryDynamicAdSlotCount
     * @param OutputInterface $output
     */
    private function migrateExpressionsForLibraryDynamicAdSlot(array $libraryDynamicAdSlots, &$migratedLibraryDynamicAdSlotCount, $output)
    {
        foreach ($libraryDynamicAdSlots as $libraryDynamicAdSlot) {
            if (!$libraryDynamicAdSlot instanceof LibraryDynamicAdSlotInterface
            ) {
                continue;
            }

            // migrate lib dynamic ad slot
            $libExpressions = $libraryDynamicAdSlot->getLibraryExpressions();
            if ($libExpressions instanceof Collection) {
                $libExpressions = $libExpressions->toArray();
            }

            $this->migrateLibExpressions($libExpressions, $output);

            $migratedLibraryDynamicAdSlotCount++;

            // save
            $this->libraryDynamicAdSlotManager->save($libraryDynamicAdSlot);
        }
    }

    /**
     * @param array|LibraryExpressionInterface[] $libExpressions
     * @param OutputInterface $output
     */
    private function migrateLibExpressions(array &$libExpressions, OutputInterface $output)
    {
        foreach ($libExpressions as &$libExpression) {
            if (!$libExpression instanceof LibraryExpressionInterface) {
                continue;
            }

            $expressionDescriptor = $libExpression->getExpressionDescriptor();
            if (!is_array($expressionDescriptor)) {
                continue;
            }

            /*
             * "expressionDescriptor" example:
             * {
             *     "groupType":"AND",
             *     "groupVal":[
             *         {
             *             "var":"${USER_AGENT}", // using $INTERNAL_VARIABLE
             *             "cmp":"contains",
             *             "val":"blackberry",
             *             "type":"string"
             *         }
             *     ]
             * }
             */
            $this->migrateExpressionDescriptor($expressionDescriptor);

            // update back to lib expression
            $libExpression->setExpressionDescriptor($expressionDescriptor);
        }
    }

    /**
     * @param array $expressionDescriptor
     */
    private function migrateExpressionDescriptor(array &$expressionDescriptor)
    {
        //try to get groupType, if not null => is group, else is not group
        $groupType = (isset($expressionDescriptor[ExpressionInJsGenerator::KEY_GROUP_TYPE]))
            ? ExpressionInJsGenerator::$VAL_GROUPS[$expressionDescriptor[ExpressionInJsGenerator::KEY_GROUP_TYPE]]
            : null;

        ($groupType != null)
            ? $this->updateForGroupObject($expressionDescriptor[ExpressionInJsGenerator::KEY_GROUP_VAL])
            : $this->updateForConditionObject($expressionDescriptor);
    }

    /**
     * @param array $expressionAsGroup
     */
    private function updateForGroupObject(array &$expressionAsGroup)
    {
        //not really needed? already verified before in formType?
        if ($expressionAsGroup == null || count($expressionAsGroup) < 1) {
            return;
        }

        // special case: check if is single condition object in one group
        if (count($expressionAsGroup) == 1) {
            $expressionDescriptor = $expressionAsGroup[0];
            $this->updateForConditionObject($expressionDescriptor);

            // remove
            if ($expressionDescriptor == null) {
                unset($expressionAsGroup[0]);
            }

            return;
        }

        foreach ($expressionAsGroup as $idx => &$expressionDescriptor) {
            $this->migrateExpressionDescriptor($expressionDescriptor);

            // remove
            if ($expressionDescriptor == null) {
                unset($expressionAsGroup[$idx]);
            }
        }

        // re-index after unset
        $expressionAsGroup = array_values($expressionAsGroup);
    }

    /**
     * @param array $expressionDescriptor
     */
    private function updateForConditionObject(array &$expressionDescriptor)
    {
        if (!array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_VAR, $expressionDescriptor)
            || !array_key_exists(ExpressionInJsGenerator::KEY_EXPRESSION_CMP, $expressionDescriptor)
        ) {
            return;
        }

        $var = $expressionDescriptor[ExpressionInJsGenerator::KEY_EXPRESSION_VAR];
        $cmp = $expressionDescriptor[ExpressionInJsGenerator::KEY_EXPRESSION_CMP];

        // remove expressions related to ${DOMAIN} that have old comparators (such as startsWith, endsWith, ...)
        if ($var === '${DOMAIN}' && !in_array($cmp, ExpressionInJsGenerator::$EXPRESSION_CMP_VALUES_FOR_STRING_DOMAIN)) {
            $expressionDescriptor = null; // null for unset
        }
    }
}