<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class UploadWhiteListAndBlackListCommand extends ContainerAwareCommand
{
    const BLACK_LIST_OPTION = 0;
    const WHITE_LIST_OPTION = 1;
    const MAX_SIZE_NAME = 100;

    protected function configure()
    {
        $this
            ->setName('tc:domains:import')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type 0 is black list and type 1 is white list')
            ->addOption('filePath', 'f', InputOption::VALUE_REQUIRED, 'file to import')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Publisher id')
            ->addOption('name' , 'd', InputOption::VALUE_REQUIRED, 'name of the list')
            ->setDescription('import list of domains from file to blacklist or white list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId = filter_var($input->getOption('id'), FILTER_VALIDATE_INT);
        $type = filter_var($input->getOption('type'), FILTER_VALIDATE_INT);
        $name = $input->getOption('name');
        $filePath = $input->getOption('filePath');

        $container = $this->getContainer();
        $logger = $container->get('logger');

        if (!$publisherId) {
            throw new \Exception(sprintf("Id %s is not valid", $publisherId));
        }

        if ($type !== self::BLACK_LIST_OPTION && $type !== self::WHITE_LIST_OPTION) {
            throw new \Exception(sprintf("Type %s is not valid", $type));
        }

        if (!isset($name)) {
            throw new \Exception(sprintf("Name must be required"));
        } else if (strlen($name) > self::MAX_SIZE_NAME) {
            throw new \Exception(sprintf("Name %s is too long", $name));
        }

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $publisher = $publisherManager->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf("Not found that publisher %s", $publisherId));
        }

        switch ($type) {
            case self::BLACK_LIST_OPTION:
                $logger->info('Start import black list');
                $blackListImport = $container->get('tagcade.service.csv.black_list_importer');
                $count = $blackListImport->importCsv($filePath, $publisher, $name);
                $logger->info(sprintf('%d domains imported', $count));
                $logger->info('Finish import black list');

                break;
            case self::WHITE_LIST_OPTION:
                $logger->info('Start import white list');
                $whiteListImport = $container->get('tagcade.service.csv.white_list_importer');
                $count = $whiteListImport->importCsv($filePath, $publisher, $name);
                $logger->info(sprintf('%d domains imported', $count));
                $logger->info('Finish import white list');

                break;
        }
    }
}