<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'path to file'
            )
            ->addOption(
                'headerRow',
                'hr',
                InputOption::VALUE_OPTIONAL,
                'Position of the header row, starting at 1',
                1
            )
            ->addOption(
                'output',
                'out',
                InputOption::VALUE_OPTIONAL,
                'The minimum length of each data row'
            )
            ->addOption(
                'separator',
                'separator',
                InputOption::VALUE_OPTIONAL,
                'Character used as separator in CSV file'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $headerRow = $input->getOption('headerRow');
        $outputFileName = $input->getOption('output');
        $separator = $input->getOption('separator');
        /**
         * @var AdSlotImporterInterface $adSlotImporter
         */
        $adSlotImporter = $this->getContainer()->get('tagcade.service.csv.ad_slot_importer');

        $output->writeln('start importing...');

        $result = $adSlotImporter->importCsv($file, $headerRow, $outputFileName, $separator);

        $output->writeln(sprintf('<info>%d site and %d ad slot get inserted successfully!</info>', $result[AdSlotImporter::SITE_KEY], $result[AdSlotImporter::SLOT_KEY]) );
        $output->writeln(sprintf('<info>The output file locate at %s</info>', $result[AdSlotImporter::OUTPUT_FILE_KEY]));
    }
}