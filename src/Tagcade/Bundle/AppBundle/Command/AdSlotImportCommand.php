<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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

        /**
         * @var AdSlotImporterInterface $adSlotImporter
         */
        $adSlotImporter = $this->getContainer()->get('tagcade.service.csv.ad_slot_importer');
        $output->writeln('start importing...');
        $result = $adSlotImporter->import($file);

        $output->writeln(sprintf('%d site and %d ad slot get inserted successfully!', $result['site'], $result['slot']) );
    }
}