<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\AdminApiBundle\Form\Type\UserFormType;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class MigrateColumnViewSettingsPerformanceReportCommand extends ContainerAwareCommand
{
    const VERSION_HIDE_CLICKS_AND_FILL_RATE_AND_UNVERIFIED_IMPRESSIONS = 1;

    private static $ALL_VERSION_DESCRIPTORS = [
        self::VERSION_HIDE_CLICKS_AND_FILL_RATE_AND_UNVERIFIED_IMPRESSIONS => 'Migrate Column View Settings for PerformanceReport: hide Clicks, Fill Rate, Unverified Impressions by default',
    ];

    private static $currentVersion = self::VERSION_HIDE_CLICKS_AND_FILL_RATE_AND_UNVERIFIED_IMPRESSIONS;

    /** @var PublisherManagerInterface */
    private $publisherManager;

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $description = self::$ALL_VERSION_DESCRIPTORS[self::$currentVersion];

        $this
            ->setName('tc:migration:column-view-settings:performance-report:update')
            ->setDescription($description);
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Running MigrateColumnViewSettingsPerformanceReportCommand...');

        $this->publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');

        /** @var Collection|PublisherInterface[] $allPublishers */
        $allPublishers = $this->publisherManager->all();
        if ($allPublishers instanceof Collection) {
            $allPublishers = $allPublishers->toArray();
        }

        if (!is_array($allPublishers)) {
            $output->writeln('Command finished: found no Publisher on this system');
            return;
        }

        $migratedCount = 0;
        switch (self::$currentVersion) {
            case self::VERSION_HIDE_CLICKS_AND_FILL_RATE_AND_UNVERIFIED_IMPRESSIONS:
                $fieldsToBeHidden = [
                    'clicks',
                    'fillRate',
                    'unverifiedImpressions'
                ];

                foreach ($allPublishers as $publisher) {
                    $publisher = $this->migrateHideClickAndFillRateAndUnverifiedImpressionsInColumnViewSettingsForPublisher($publisher, $fieldsToBeHidden);

                    // update
                    if ($publisher instanceof PublisherInterface) {
                        $migratedCount++;
                        $this->publisherManager->save($publisher);
                    }
                }

                break;

            default:
                $output->writeln(sprintf('Command finished with error: unknown version ', self::$currentVersion));
                return;
        }

        $output->writeln(sprintf('Command runs successfully: migrated %d Publishers.', $migratedCount));
    }

    /**
     * @param PublisherInterface $publisher
     * @param array $fieldsToBeHidden
     * @return bool|UserInterface|PublisherInterface false if not need migrate
     */
    private function migrateHideClickAndFillRateAndUnverifiedImpressionsInColumnViewSettingsForPublisher(PublisherInterface $publisher, array $fieldsToBeHidden)
    {
        $settings = $publisher->getSettings();
        if (!is_array($settings)) {
            return false; // not need update
        }

        if (!isset($settings['view']['report']['performance']['adTag'])) {
            return false; // not need update
        }

        $adTagConfigs = $settings['view']['report']['performance']['adTag'];
        $migrated = false;

        foreach ($adTagConfigs as &$adTagConfig) {
            // keys 'key', 'label, 'show' are required
            if (!isset($adTagConfig['key'])
                || !isset($adTagConfig['label'])
                || !isset($adTagConfig['show'])
            ) {
                continue; // skip this item
            }

            // 'key' must be supported
            if (!in_array($adTagConfig['key'], UserFormType::$REPORT_SETTINGS_PF_ADTAG_KEY_VALUES)) {
                continue; // skip this item
            }

            // 'key' must be in $fieldsToBeHidden
            if (!in_array($adTagConfig['key'], $fieldsToBeHidden)) {
                continue; // skip this item
            }

            // skip if is already hidden
            if (!$adTagConfig['show']) {
                continue; // skip this item
            }

            // DO hide by default
            $adTagConfig['show'] = false;
            $migrated = true;
        }

        if (!$migrated) {
            return false;
        }

        // update back to publisher if migrated settings
        $settings['view']['report']['performance']['adTag'] = $adTagConfigs;
        $publisher->setSettings($settings);

        return $publisher;
    }
}