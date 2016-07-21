<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;


class BulkUploadCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:api:bulk-upload')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'path to file to be imported')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'publisher add data')
            ->addOption('dry','D',InputOption::VALUE_OPTIONAL,'dry run option',false)
            ->setDescription('Bulk upload data for sites, display ad slots, ad tag and dynamic ad slots');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');
        $logger->info('Start running upload data!');

        $file = $input->getOption('file');
        $publisherId = $input->getOption('publisher');

        $logger->debug(sprintf('File to get data %s', $file));

        $dir = $this->getContainer()->getParameter('kernel.root_dir');
        $rootDir = rtrim($dir, '/app');
        if (strpos($file, '/') != 0) { // relative path
            $file = ltrim($file, './');
            $file = sprintf('%s/%s', $rootDir, $file);
        }

        if (!is_file($file)) {
            throw new \Exception(sprintf('The specified file in not found or not accessible %s', $file));
        }
        $logger->debug(sprintf('Full path to data file %s', $file));

        $container = $this->getContainer();

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $excelFileProcessingService = $container->get('tagcade_app.service.core.excel_file_processing');

        $siteImportBulkDataServer = $container->get('tagcade_app.service.core.site.bulk_upload');
        $publisher = $publisherManager->find($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('Not exist Publisher with id =%d', $publisherId));
        }

        $dryOption = $input->getOption('dry');
        $contents = $excelFileProcessingService->getAllContentExcelFile($file);

        $logger->info('Begin import sites to system');
        $siteWithFullData = $siteImportBulkDataServer->createFullDataForSites($contents,$publisher);
        $siteImportBulkDataServer->createSites($siteWithFullData, $publisher, $dryOption);
        $logger->info('End import sites to system');
    }
} 