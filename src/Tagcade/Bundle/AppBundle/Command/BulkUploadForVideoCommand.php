<?php


namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Behaviors\ParserUtilTrait;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\ExcelFileProcessing;
use Tagcade\Service\Importer\VideoDemandAdTagImporterInterface;
use Tagcade\Service\Importer\VideoDemandPartnerImporterInterface;
use Tagcade\Service\Importer\VideoPublisherImporterInterface;
use Tagcade\Service\Importer\VideoWaterfallTagImporterInterface;

class BulkUploadForVideoCommand extends ContainerAwareCommand
{
    use ParserUtilTrait;

    const COMMAND_NAME = 'tc:video:bulk-upload';
    const FILE = 'file';
    const DRY = 'dry';
    const OVERWRITE = 'overwrite';
    const PUBLISHER = 'publisher';

    /** @var  SymfonyStyle */
    private $io;

    /** @var  ExcelFileProcessing */
    private $excelFileProcessingService;

    /** @var  VideoPublisherImporterInterface */
    private $videoPublisherImporter;

    /** @var  VideoDemandPartnerImporterInterface */
    private $videoDemandPartnerImporter;

    /** @var  VideoDemandAdTagImporterInterface */
    private $videoDemandAdTagImporter;

    /** @var  VideoWaterfallTagImporterInterface */
    private $videoWaterfallTagImporter;

    /** @var  PublisherManagerInterface */
    private $publisherManager;


    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->addArgument(self::PUBLISHER, InputArgument::REQUIRED, 'publisher add data')
            ->addOption(self::FILE, 'f', InputOption::VALUE_REQUIRED, 'path to file to be imported')
            ->addOption(self::DRY, 'd', InputOption::VALUE_NONE, 'dry run option')
            ->addOption(self::OVERWRITE, 'o', InputOption::VALUE_NONE, 'Force update info from Excel to existing objects')
            ->setDescription('Bulk upload data for publisher, video waterfalls, ad tags');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $this->videoPublisherImporter = $container->get('tagcade_app.service.importer.video_publisher_importer');
        $this->videoDemandPartnerImporter = $container->get('tagcade_app.service.importer.video_demand_partner_importer');
        $this->videoDemandAdTagImporter = $container->get('tagcade_app.service.importer.video_demand_ad_tag_importer');
        $this->videoWaterfallTagImporter = $container->get('tagcade_app.service.importer.video_waterfall_tag_importer');
        $this->excelFileProcessingService = $container->get('tagcade_app.service.core.excel_file_processing');

        $this->io = new SymfonyStyle($input, $output);
        $this->io->section('Start running upload data!');

        $publisherId = $input->getArgument(self::PUBLISHER);
        $dryOption = $input->getOption(self::DRY);
        $file = $input->getOption(self::FILE);
        $overwrite = $input->getOption(self::OVERWRITE);

        $publisher = $this->publisherManager->findPublisher($publisherId);
        if (!$publisher instanceof PublisherInterface) {
            $this->io->warning(sprintf('Can not find publisher id = %s. Quit command', $publisherId));
            return;
        }

        $this->io->text(sprintf('File to get data %s', $file));
        $dir = $container->getParameter('kernel.root_dir');
        $rootDir = rtrim($dir, '/app');
        if (strpos($file, '/') != 0) { // relative path
            $file = ltrim($file, './');
            $file = sprintf('%s/%s', $rootDir, $file);
        }

        if (!is_file($file)) {
            $this->io->warning(sprintf('The specified file in not found or not accessible %s. Quit command', $file));
            return;
        }
        $this->io->text(sprintf('Full path to data file %s', $file));

        try {
            $rawContents = $this->excelFileProcessingService->getAllContentExcelFile($file);
            $contents = [];
            foreach ($rawContents as $key => $value) {
                $contents[$this->normalizeColumn($key)] = $value;
            }
        } catch (\Exception $e) {
            $this->io->warning(sprintf('Can not read data from %s. Quit command', $file));
            return;
        }

        $videoPublishers = $this->videoPublisherImporter->getVideoPublishersFromFileContents($contents, $publisher);
        if (empty($videoPublishers)) {
            $this->io->warning(sprintf('Not found any video publishers. Quit command'));
            return;
        }
        $this->videoPublisherImporter->importVideoPublishers($videoPublishers, $dryOption, $this->io);

        $videoDemandPartners = $this->videoDemandPartnerImporter->getVideoDemandPartnersFromFileContents($contents, $overwrite, $videoPublishers);
        if (empty($videoDemandPartners)) {
            $this->io->warning(sprintf('Not found any video demand partners. Quit command'));
            return;
        }
        $this->videoDemandPartnerImporter->importVideoDemandPartners($videoDemandPartners, $dryOption, $this->io, $videoPublishers);

        $videoWaterfallTags = $this->videoWaterfallTagImporter->getVideoWaterfallTagsFromFileContents($contents, $overwrite, $videoPublishers);
        if (empty($videoWaterfallTags)) {
            $this->io->warning(sprintf('Not found any video waterfall tags. Quit command'));
            return;
        }
        $this->videoWaterfallTagImporter->importVideoWaterfallTags($videoWaterfallTags, $dryOption, $this->io, $videoPublishers);

        $videoDemandAdTags = $this->videoDemandAdTagImporter->getVideoDemandAdTagsFromFileContents($contents, $overwrite, $videoDemandPartners, $videoWaterfallTags);
        if (empty($videoDemandAdTags)) {
            $this->io->warning(sprintf('Not found any library video demand ad tags. Quit command'));
            return;
        }
        $this->videoDemandAdTagImporter->importVideoDemandAdTags($videoDemandAdTags, $dryOption, $this->io);

        $this->io->success('Quit command');
    }
} 