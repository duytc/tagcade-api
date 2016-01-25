<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\CSV\AdSlotImporter;
use Tagcade\Service\CSV\AdSlotImporterInterface;

class AdSlotImportCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:adslot:import')
            ->setDescription('Import ad slot from CSV file to DB, update if ad slot existed')
            ->addOption(
                'publisher',
                'p',
                InputOption::VALUE_REQUIRED,
                'Publisher id'
            )
            ->addOption(
                'input',
                'i',
                InputOption::VALUE_REQUIRED,
                'path to file'
            )
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Output file path'
            )
            ->addOption(
                'headerRow',
                'hr',
                InputOption::VALUE_OPTIONAL,
                'Position of the header row, starting at 1',
                0
            )
            ->addOption(
                'separator',
                'se',
                InputOption::VALUE_OPTIONAL,
                'Character used as separator in CSV file',
                ','
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'force the service persisting changes to DB'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('input');
        $publisherId = $input->getOption('publisher');
        $headerRow = $input->getOption('headerRow');
        $outputFileName = $input->getOption('output');
        $separator = $input->getOption('separator');
        $force = $input->getOption('force');

        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $publisher = $publisherManager->findPublisher($publisherId);

        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception('That publisher does not exist');
        }

        /** @var AdSlotImporterInterface $adSlotImporter */
        $adSlotImporter = $this->getContainer()->get('tagcade.service.csv.ad_slot_importer');

        $output->writeln(sprintf('<info>importing data for publisher %s (%s)</info>', $publisher->getFirstName() . ' ' . $publisher->getLastName(), $publisher->getEmail()));
        $output->writeln('-----------------------------------------------------------------------------------');

        //dumping changes
        if ($force === FALSE) {
            $output->writeln('<question>running in dump mode ...</question>');
            $res = $adSlotImporter->dumpChangesFromCsvForPublisher($publisher, $file, $headerRow, $separator);
            foreach($res[AdSlotImporter::RESULT_DATA_KEY] as $i=>$site) {
                $output->writeln(sprintf('<question>%d. site "%s" (%s) is being %s</question>', $i+1, $site[AdSlotImporter::DUMP_SITE_NAME_KEY], $site[AdSlotImporter::DUMP_SITE_DOMAIN_KEY], strtoupper($site[AdSlotImporter::DUMP_SITE_STATUS_KEY])));

                if (count($site[AdSlotImporter::DUMP_NEW_SLOTS_KEY])) {
                    $output->writeln(sprintf('<info>   ad slots being inserted (%d slots):</info>', count($site[AdSlotImporter::DUMP_NEW_SLOTS_KEY])) );

                    foreach($site[AdSlotImporter::DUMP_NEW_SLOTS_KEY] as $slot) {
                        $output->writeln(sprintf('      - name : %s, width : %d, height : %d', $slot[AdSlotImporter::DUMP_SLOT_NAME_KEY], $slot[AdSlotImporter::DUMP_SLOT_WIDTH_KEY], $slot[AdSlotImporter::DUMP_SLOT_HEIGHT_KEY]));
                    }
                }

                if (count($site[AdSlotImporter::DUMP_DELETING_SLOTS_KEY])) {
                    $output->writeln(sprintf('<error>   ad slots being deleted (%d slots):</error>', count($site[AdSlotImporter::DUMP_DELETING_SLOTS_KEY])) );

                    foreach($site[AdSlotImporter::DUMP_DELETING_SLOTS_KEY] as $slot) {
                        $output->writeln(sprintf('      - name : %s, width : %d, height : %d', $slot[AdSlotImporter::DUMP_SLOT_NAME_KEY], $slot[AdSlotImporter::DUMP_SLOT_WIDTH_KEY], $slot[AdSlotImporter::DUMP_SLOT_HEIGHT_KEY]));
                    }
                }
                $output->writeln('********************************************************************************');
                $output->writeln('');
            }

            $output->writeln(sprintf('<info>- %d site(s) is being inserted.</info>', $res[AdSlotImporter::RESULT_REPORT_KEY][AdSlotImporter::DUMP_NEW_SITES_KEY]));
            $output->writeln(sprintf('<info>- %d site(s) is being updated.</info>', $res[AdSlotImporter::RESULT_REPORT_KEY][AdSlotImporter::DUMP_UPDATED_SITES_KEY]));
            $output->writeln(sprintf('<info>- %d slot(s) is being inserted.</info>', $res[AdSlotImporter::RESULT_REPORT_KEY][AdSlotImporter::DUMP_NEW_SLOTS_KEY]));
            $output->writeln(sprintf('<info>- %d slot(s) is being deleted.</info>', $res[AdSlotImporter::RESULT_REPORT_KEY][AdSlotImporter::DUMP_DELETING_SLOTS_KEY]));
            $output->writeln('<comment>Use --force to persist above changes to DB</comment>');
            return;
        }

        $output->writeln('start importing...');
        $result = $adSlotImporter->importCsvForPublisher($publisher, $file, $outputFileName, $headerRow, $separator);
        $output->writeln(sprintf('<info>%d site and %d ad slot get inserted successfully!</info>', $result[AdSlotImporter::SITE_KEY], $result[AdSlotImporter::SLOT_KEY]) );
        $output->writeln(sprintf('<info>The output file is located at %s</info>', $result[AdSlotImporter::OUTPUT_FILE_KEY]));
    }
}