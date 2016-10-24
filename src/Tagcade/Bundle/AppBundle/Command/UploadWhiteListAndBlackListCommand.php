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
            ->setName('tc:list:upload-white-and-black')
            ->addOption('type', 't', InputOption::VALUE_REQUIRED, 'Type 0 is black list and type 1 is white list')
            ->addOption('filePath', 'f', InputOption::VALUE_OPTIONAL, 'Path of imported file')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'Publisher id')
            ->addOption('name' , 'd', InputOption::VALUE_REQUIRED, 'Blacklist/whitelist name')
            ->setDescription('Upload black list and white list for publisher.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $publisherId = $input->getOption('id');
        $type = $input->getOption('type');
        $name = $input->getOption('name');
        $filePath = $input->getOption('filePath');
        $container = $this->getContainer();
        $logger = $container->get('logger');

        if (!preg_match('/^[1-9]\d*$/', $publisherId)) {
            throw new \Exception(sprintf("Publisher id %s is not valid", $publisherId));
        }

        if (!preg_match('/^[0-1]$/', $type)) {
            throw new \Exception(sprintf("Type %s is not valid", $type));
        }

        if (strlen($name) > self::MAX_SIZE_NAME) {
            throw new \Exception(sprintf("Name %s is not valid", $name));
        }

        if($filePath && !file_exists($filePath)  && is_file($filePath)) {
            throw new \Exception(sprintf("File %s is not exits", $filePath));
        }

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $publisher = $publisherManager->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf("Not found that publisher %s", $publisherId));
        }

        if($type == self::BLACK_LIST_OPTION) {
            $logger->info('start import black list');
            $blackListImport = $container->get('tagcade.service.csv.black_list_importer');
            $blackListImport->importCsv($filePath, $publisher, $name);
            $logger->info('Finish import black list');
        } else if ($type == self::WHITE_LIST_OPTION) {
            $logger->info('start import white list');
            $whiteListImport = $container->get('tagcade.service.csv.white_list_importer');
            $whiteListImport->importCsv($filePath, $publisher, $name);
            $logger->info('Finish import white list');
        }
    }
}