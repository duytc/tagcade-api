<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;


class BulkUploadCommand extends ContainerAwareCommand
{
    const SITE_SHEET_NAME               =   'Sites';
    const AD_TAGS_SHEET_NAME            =   'Ad Tags';
    const DISPLAY_AD_SLOT_NAME          =   'Display Ad Slots';
    const DYNAMIC_AD_SLOT_NAME          =   'Dynamic Ad Slots';
    const EXPRESSION_TARGETING_NAME     =   'Expression Targeting';

    protected function configure()
    {
        $this
            ->setName('tc:api:bulk-upload')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'path to file to be imported')
            ->addOption('publisher', 'p', InputOption::VALUE_REQUIRED, 'publisher add data')
            ->addOption('dry','D',InputOption::VALUE_OPTIONAL,'dry run option',false)
            ->setDescription('Bulk upload data for site, display ad slot, ad tag and dynamic ad slot');
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

        if(!is_file($file)) {
            throw new \Exception(sprintf('The specified file in not found or not accessible %s', $file));
        }
        $logger->debug(sprintf('Full path to data file %s', $file));

        $container = $this->getContainer();

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $excelFileProcessingService = $container->get('tagcade_app.service.core.excel_file_processing');

        $siteImportBulkDataServer = $container->get('tagcade_app.service.core.site.bulk_upload');
        $displayAdSlotImportBulkService = $container->get('tagcade_app.service.core.display_ad_slot.bulk_upload');
        $adTagImportBulkService = $container->get('tagcade_app.service.core.ad_tag.bulk_upload');

        $publisher = $publisherManager->find($publisherId);
        if(!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('Not exist Publisher with id =%d', $publisherId));
        }

        $dryOption = $input->getOption('dry');
        $contents = $excelFileProcessingService->getAllContentExcelFile($file);

        $sites = $this->getSitesFromExcelArray($contents);
        $displayAdSlotData = $this->getDisplayAdSlotsFromExcelArray($contents);
        $adTagsData = $this->getAdTagsFromExcelArray($contents);
        $dynamicAdSlotData =$this->getDynamicAdSlotsFromExcelArray($contents);
        $expressionTargeting = $this->getExpressionTargetingFromExcelArray($contents);

        $sitesMapArray = $siteImportBulkDataServer->createDataForSites($sites, $publisher);
        $displaysAdSlotMapsArray = $displayAdSlotImportBulkService->createAllDisplayAdSlotsData($displayAdSlotData, $publisher);
        $adTagsMapArray = $adTagImportBulkService->createAllAdTagsData($adTagsData, $publisher);

        $logger->info('Begin import sites to system');
        $siteImportBulkDataServer->createSites($sitesMapArray, $publisher, $displaysAdSlotMapsArray, $dynamicAdSlotData, $expressionTargeting, $adTagsMapArray, $dryOption);
        $logger->info('End import sites to system');
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getSitesFromExcelArray($contents)
    {
        $sites = $contents[self::SITE_SHEET_NAME];
        array_shift($sites); // Remove header of site sheet

        return $sites;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getDisplayAdSlotsFromExcelArray($contents)
    {
       $displayAdSlotData = $contents[self::DISPLAY_AD_SLOT_NAME];
       array_shift($displayAdSlotData); // Remove header of display ad slot

        return $displayAdSlotData;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getAdTagsFromExcelArray($contents)
    {
        $adTagsData = $contents[self::AD_TAGS_SHEET_NAME];
        array_shift($adTagsData); // Remove header of display ad slot

        return $adTagsData;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getDynamicAdSlotsFromExcelArray($contents)
    {
        $dynamicAdSlotData = $contents[self::DYNAMIC_AD_SLOT_NAME];
        array_shift($dynamicAdSlotData); // Remove header of display ad slot

        return $dynamicAdSlotData;
    }

    /**
     * @param $contents
     * @return mixed
     */
    protected function getExpressionTargetingFromExcelArray($contents)
    {
        $expressionTargeting = $contents[self::EXPRESSION_TARGETING_NAME];
        array_shift($expressionTargeting); // Remove header of expression targeting

        return $expressionTargeting;
    }

} 