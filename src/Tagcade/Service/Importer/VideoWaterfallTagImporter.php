<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Behaviors\ParserUtilTrait;
use Tagcade\Behaviors\VideoUtilTrait;
use Tagcade\DomainManager\VideoPublisherManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Entity\Core\VideoWaterfallTag;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class VideoWaterfallTagImporter implements VideoWaterfallTagImporterInterface
{
    use ParserUtilTrait;
    use VideoUtilTrait;

    const SHEET_NAME = 'videowaterfalls';

    const PUBLISHER = 'videopublisher';
    const NAME = 'waterfallname';
    const BUY_PRICE = 'buyprice';
    const PLATFORM = 'platform';
    const AD_DURATION = 'adduration';
    const RUN_ON = 'runon';


    /** @var  SymfonyStyle */
    private $io;

    /** @var  VideoWaterfallTagManagerInterface */
    private $videoWaterfallTagManager;

    /** @var  VideoPublisherManagerInterface */
    private $videoPublisherManager;

    /**
     * VideoWaterfallTagImporter constructor.
     * @param VideoWaterfallTagManagerInterface $videoWaterfallTagManager
     * @param VideoPublisherManagerInterface $videoPublisherManager
     */
    public function __construct(VideoWaterfallTagManagerInterface $videoWaterfallTagManager, VideoPublisherManagerInterface $videoPublisherManager)
    {
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
        $this->videoPublisherManager = $videoPublisherManager;
    }

    /**
     * @inheritdoc
     */
    public function importVideoWaterfallTags($videoWaterfallTags, $dryOption, SymfonyStyle $io, $videoPublishers)
    {
        $this->io = $io;

        $this->io->section(sprintf('Begin import %s video waterfall tags to system', count($videoWaterfallTags)));

        foreach ($videoWaterfallTags as $videoWaterfallTag) {
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            if ($dryOption) {
                $this->io->note(sprintf('Dry run: Prepare for video waterfall tag: name = %s', $videoWaterfallTag->getName()));
                continue;
            }

            $this->videoWaterfallTagManager->save($videoWaterfallTag);
            $this->io->note(sprintf('Create/Update video waterfall tag: name = %s, id = %s', $videoWaterfallTag->getName(), $videoWaterfallTag->getId()));
        }

        $this->io->success('Success import video waterfall tags to system');

        return $videoWaterfallTags;
    }

    /**
     * @inheritdoc
     */
    public function getVideoWaterfallTagsFromFileContents($excelRows, $overwrite, $videoPublishers)
    {
        $videoWaterfallTags = [];

        if (!array_key_exists(self::SHEET_NAME, $excelRows)) {
            return $videoWaterfallTags;
        }

        $videoWaterfallTagRows = $excelRows[self::SHEET_NAME];
        $headers = reset($videoWaterfallTagRows);
        $headers = $this->normalizeColumns($headers);

        foreach ($videoWaterfallTagRows as $key => $excelRow) {
            if ($key == 0 || count($headers) != count($excelRow)) {
                continue;
            }

            $excelRow = array_combine($headers, $excelRow);
            $videoWaterfallTag = $this->createVideoWaterfallTagDataFromExcelRow($excelRow, $overwrite, $videoPublishers);
            if (!$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
                continue;
            }

            $videoWaterfallTags[$videoWaterfallTag->getName()] = $videoWaterfallTag;
        }

        return $videoWaterfallTags;
    }

    /**
     * @param $excelRow
     * @param $overwrite
     * @param $videoPublishers
     * @return null|VideoWaterfallTagInterface
     */
    private function createVideoWaterfallTagDataFromExcelRow($excelRow, $overwrite, $videoPublishers)
    {
        $name = array_key_exists(self::NAME, $excelRow) ? $excelRow[self::NAME] : null;
        $videoPublisherPos = array_key_exists(self::PUBLISHER, $excelRow) ? $excelRow[self::PUBLISHER] : null;
        $adDuration = array_key_exists(self::AD_DURATION, $excelRow) ? $excelRow[self::AD_DURATION] : 0;
        $buyPrice = array_key_exists(self::BUY_PRICE, $excelRow) ? $excelRow[self::BUY_PRICE] : null;
        $platform = array_key_exists(self::PLATFORM, $excelRow) ? [$excelRow[self::PLATFORM]] : [];
        $runOn = array_key_exists(self::RUN_ON, $excelRow) ? $excelRow[self::RUN_ON] : null;

        $videoPublisher = array_key_exists($videoPublisherPos, $videoPublishers) ? $videoPublishers[$videoPublisherPos] : null;
        if (!$videoPublisher instanceof VideoPublisherInterface || empty($name)) {
            return null;
        }

        $videoWaterfallTag = $this->videoWaterfallTagManager->findByNameAndVideoPublisher($name, $videoPublisher);
        $videoWaterfallTag = is_array($videoWaterfallTag) ? reset($videoWaterfallTag) : $videoWaterfallTag;

        if ($videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            if ($overwrite) {
                $videoWaterfallTag
                    ->setVideoPublisher($videoPublisher)
                    ->setAdDuration($adDuration)
                    ->setBuyPrice($buyPrice)
                    ->setName($name)
                    ->setPlatform($platform)
                    ->setRunOn($runOn);
            }
            
            return $videoWaterfallTag;
        }

        $videoWaterfallTag = new VideoWaterfallTag();

        $videoWaterfallTag
            ->setVideoPublisher($videoPublisher)
            ->setAdDuration($adDuration)
            ->setBuyPrice($buyPrice)
            ->setName($name)
            ->setPlatform($platform)
            ->setRunOn($runOn);

        $videoWaterfallTag = $this->addDefaultValueForVideoWaterfallTag($videoWaterfallTag);

        return $videoWaterfallTag;
    }
}