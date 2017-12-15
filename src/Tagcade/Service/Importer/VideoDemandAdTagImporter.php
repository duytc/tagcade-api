<?php


namespace Tagcade\Service\Importer;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Behaviors\ParserUtilTrait;
use Tagcade\Behaviors\VideoUtilTrait;
use Tagcade\DomainManager\LibraryVideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\DomainManager\VideoDemandPartnerManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagItemManagerInterface;
use Tagcade\DomainManager\VideoWaterfallTagManagerInterface;
use Tagcade\Entity\Core\LibraryVideoDemandAdTag;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Entity\Core\VideoWaterfallTagItem;
use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Repository\Core\VideoWaterfallTagItemRepositoryInterface;

class VideoDemandAdTagImporter implements VideoDemandAdTagImporterInterface
{
    use ParserUtilTrait;
    use VideoUtilTrait;

    const SHEET_NAME = 'adtags';

    const WATERFALL_NAME = 'waterfall';
    const DEMAND_PARTNER = 'demandpartner';
    const TAG_NAME = 'tagname';
    const SELL_PRICE = 'sellprice';
    const TAG_URL = 'tagurl';
    const REQUEST_TIMEOUT = 'requesttimeout';
    const REQUEST_CAP = 'requestcap';
    const IMPRESSION_CAP = 'impressioncap';
    const WEIGHT = 'weight';


    /** @var  SymfonyStyle */
    private $io;

    /** @var  LibraryVideoDemandAdTagManagerInterface */
    private $libraryVideoDemandAdTagManager;

    /** @var  VideoDemandPartnerManagerInterface */
    private $videoDemandPartnerManager;

    /** @var  VideoWaterfallTagManagerInterface */
    private $videoWaterfallTagManager;

    /** @var  VideoWaterfallTagItemManagerInterface */
    private $videoWaterfallTagItem;

    /** @var  VideoDemandAdTagManagerInterface */
    private $videoDemandAdTagManager;

    /** @var  VideoWaterfallTagItemRepositoryInterface */
    private $videoWaterfallTagItemRepository;

    /**
     * VideoDemandAdTagImporter constructor.
     * @param LibraryVideoDemandAdTagManagerInterface $libraryVideoDemandAdTagManager
     * @param VideoDemandPartnerManagerInterface $videoDemandPartnerManager
     * @param VideoWaterfallTagManagerInterface $videoWaterfallTagManager
     * @param VideoWaterfallTagItemManagerInterface $videoWaterfallTagItem
     * @param VideoDemandAdTagManagerInterface $videoDemandAdTagManager
     * @param VideoWaterfallTagItemRepositoryInterface $videoWaterfallTagItemRepository
     */
    public function __construct(LibraryVideoDemandAdTagManagerInterface $libraryVideoDemandAdTagManager, VideoDemandPartnerManagerInterface $videoDemandPartnerManager, VideoWaterfallTagManagerInterface $videoWaterfallTagManager, VideoWaterfallTagItemManagerInterface $videoWaterfallTagItem, VideoDemandAdTagManagerInterface $videoDemandAdTagManager, VideoWaterfallTagItemRepositoryInterface $videoWaterfallTagItemRepository)
    {
        $this->libraryVideoDemandAdTagManager = $libraryVideoDemandAdTagManager;
        $this->videoDemandPartnerManager = $videoDemandPartnerManager;
        $this->videoWaterfallTagManager = $videoWaterfallTagManager;
        $this->videoWaterfallTagItem = $videoWaterfallTagItem;
        $this->videoDemandAdTagManager = $videoDemandAdTagManager;
        $this->videoWaterfallTagItemRepository = $videoWaterfallTagItemRepository;
    }

    /**
     * @inheritdoc
     */
    public function importVideoDemandAdTags($videoDemandAdTags, $dryOption, SymfonyStyle $io)
    {
        $this->io = $io;

        $this->io->section(sprintf('Begin import %s video demand ad tags to system', count($videoDemandAdTags)));

        foreach ($videoDemandAdTags as $videoDemandAdTag) {
            if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) {
                continue;
            }

            if ($dryOption) {
                $this->io->note(sprintf('Dry run: Prepare for video demand ad tag: name = %s', $videoDemandAdTag->getName()));
                continue;
            }

            $this->videoDemandAdTagManager->save($videoDemandAdTag);
            $this->io->note(sprintf('Create/Update video demand ad tag: name = %s, id = %s', $videoDemandAdTag->getName(), $videoDemandAdTag->getId()));
        }

        $this->io->success('Success import video demand ad tags to system');

        return $videoDemandAdTags;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandAdTagsFromFileContents($excelRows, $overwrite, $videoDemandPartners, $videoWaterfallTags)
    {
        $videoDemandAdTags = [];

        if (!array_key_exists(self::SHEET_NAME, $excelRows)) {
            return $videoDemandAdTags;
        }

        $adTagRows = $excelRows[self::SHEET_NAME];
        $headers = reset($adTagRows);
        $headers = $this->normalizeColumns($headers);

        foreach ($adTagRows as $key => $excelRow) {
            if ($key == 0 || count($headers) != count($excelRow)) {
                continue;
            }

            $excelRow = array_combine($headers, $excelRow);

            $libraryVideoDemandAdTag = $this->createLibraryVideoDemandAdTagsFromExcelRow($excelRow, $overwrite, $videoDemandPartners);
            $videoDemandAdTag = $this->createVideoDemandAdTagsFromExcelRow($excelRow, $overwrite, $videoWaterfallTags);

            if (!$videoDemandAdTag instanceof VideoDemandAdTagInterface) {
                continue;
            }

            $videoDemandAdTag->setLibraryVideoDemandAdTag($libraryVideoDemandAdTag);
            $videoDemandAdTag = $this->addDefaultValuesForVideoDemandAdTag($videoDemandAdTag);

            $videoDemandAdTags[] = $videoDemandAdTag;
        }

        return $videoDemandAdTags;
    }

    /**
     * @param $excelRow
     * @param $overwrite
     * @param $videoWaterfallTags
     * @return null|VideoDemandAdTagInterface
     */
    private function createVideoDemandAdTagsFromExcelRow($excelRow, $overwrite, $videoWaterfallTags)
    {
        $demandPartnerName = array_key_exists(self::DEMAND_PARTNER, $excelRow) ? $excelRow[self::DEMAND_PARTNER] : null;
        $waterFallName = array_key_exists(self::WATERFALL_NAME, $excelRow) ? $excelRow[self::WATERFALL_NAME] : null;
        $tagName = array_key_exists(self::TAG_NAME, $excelRow) ? $excelRow[self::TAG_NAME] : null;
        $requestCap = array_key_exists(self::REQUEST_CAP, $excelRow) ? $excelRow[self::REQUEST_CAP] : null;
        $impressionCap = array_key_exists(self::IMPRESSION_CAP, $excelRow) ? $excelRow[self::IMPRESSION_CAP] : null;
        $timeOut = array_key_exists(self::REQUEST_TIMEOUT, $excelRow) ? $excelRow[self::REQUEST_TIMEOUT] : null;
        $videoWaterfallTag = array_key_exists($waterFallName, $videoWaterfallTags) ? $videoWaterfallTags[$waterFallName] : null;
        $tagURL = array_key_exists(self::TAG_URL, $excelRow) ? $excelRow[self::TAG_URL] : null;

        if (empty($demandPartnerName) || empty($waterFallName) || empty($tagName) || !$this->validateDomainWithHTTP($tagURL) || !$videoWaterfallTag instanceof VideoWaterfallTagInterface) {
            return null;
        }

        $videoDemandAdTag = $this->videoDemandAdTagManager->findByDemandPartnerWaterfallAndTagName($demandPartnerName, $waterFallName, $tagName);
        $videoDemandAdTag = is_array($videoDemandAdTag) ? reset($videoDemandAdTag) : $videoDemandAdTag;

        if ($videoDemandAdTag instanceof VideoDemandAdTagInterface) {
            if ($overwrite) {
                $videoDemandAdTag
                    ->setRequestCap($requestCap)
                    ->setImpressionCap($impressionCap)
                    ->setTimeout($timeOut);
            }

            return $videoDemandAdTag;
        }

        $videoDemandAdTag = new VideoDemandAdTag();

        $videoDemandAdTag
            ->setRequestCap($requestCap)
            ->setImpressionCap($impressionCap)
            ->setTimeout($timeOut);

        $videoDemandAdTag = $this->addDefaultValueForVideoDemandAdTag($videoDemandAdTag);

        if (!$videoDemandAdTag->getVideoWaterfallTagItem() instanceof VideoWaterfallTagItemInterface) {
            $videoWaterfallTagItems = $videoWaterfallTag->getVideoWaterfallTagItems();

            if ($videoWaterfallTagItems instanceof Collection) {
                $videoWaterfallTagItems = $videoWaterfallTagItems->toArray();
            }

            if ($videoWaterfallTag instanceof VideoWaterfallTagInterface && empty($videoWaterfallTagItems)) {
                $lastPosition = (int)$this->videoWaterfallTagItemRepository->getMaxPositionInWaterfallTag($videoWaterfallTag);

                $videoWaterfallTagItem = (new VideoWaterfallTagItem())
                    ->setStrategy('parallel')
                    ->setVideoWaterfallTag($videoWaterfallTag)
                    ->addVideoDemandAdTag($videoDemandAdTag)
                    ->setPosition($lastPosition + 1);

                $this->videoWaterfallTagItem->save($videoWaterfallTagItem);
                $videoWaterfallTagItems[] = $videoWaterfallTagItem;
            }

            $videoDemandAdTag->setVideoWaterfallTagItem(end($videoWaterfallTagItems));
        }

        return $videoDemandAdTag;
    }

    /**
     * @param $excelRow
     * @param $overwrite
     * @param $videoDemandPartners
     * @return null|LibraryVideoDemandAdTagInterface
     */
    private function createLibraryVideoDemandAdTagsFromExcelRow($excelRow, $overwrite, $videoDemandPartners)
    {
        $tagName = array_key_exists(self::TAG_NAME, $excelRow) ? $excelRow[self::TAG_NAME] : null;
        $tagURL = array_key_exists(self::TAG_URL, $excelRow) ? $excelRow[self::TAG_URL] : null;
        $sellPrice = array_key_exists(self::SELL_PRICE, $excelRow) ? $excelRow[self::SELL_PRICE] : null;
        $demandPartnerName = array_key_exists(self::DEMAND_PARTNER, $excelRow) ? $excelRow[self::DEMAND_PARTNER] : null;
        $timeOut = array_key_exists(self::REQUEST_TIMEOUT, $excelRow) ? $excelRow[self::REQUEST_TIMEOUT] : null;
        $demandPartner = array_key_exists($demandPartnerName, $videoDemandPartners) ? $videoDemandPartners[$demandPartnerName] : null;

        if (!$demandPartner instanceof VideoDemandPartnerInterface || empty($tagName) || !$this->validateDomainWithHTTP($tagURL)) {
            return null;
        }

        $libraryVideoDemandAdTag = $this->libraryVideoDemandAdTagManager->findByNameAndVideoDemandPartner($tagName, $demandPartner);
        $libraryVideoDemandAdTag = is_array($libraryVideoDemandAdTag) ? reset($libraryVideoDemandAdTag) : $libraryVideoDemandAdTag;

        if ($libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            if ($overwrite) {
                $libraryVideoDemandAdTag
                    ->setVideoDemandPartner($demandPartner)
                    ->setName($tagName)
                    ->setTagURL($tagURL)
                    ->setSellPrice($sellPrice)
                    ->setTimeout($timeOut);
            }

            return $libraryVideoDemandAdTag;
        }

        $libraryVideoDemandAdTag = new LibraryVideoDemandAdTag();

        $libraryVideoDemandAdTag
            ->setVideoDemandPartner($demandPartner)
            ->setName($tagName)
            ->setTagURL($tagURL)
            ->setSellPrice($sellPrice)
            ->setTimeout($timeOut);

        if (!$libraryVideoDemandAdTag instanceof LibraryVideoDemandAdTagInterface) {
            return null;
        }

        $libraryVideoDemandAdTag = $this->addDefaultValueForLibraryVideoDemandAdTag($libraryVideoDemandAdTag);

        return $libraryVideoDemandAdTag;
    }
}