<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Behaviors\ParserUtilTrait;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\VideoPublisherManagerInterface;
use Tagcade\Entity\Core\VideoPublisher;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class VideoPublisherImporter implements VideoPublisherImporterInterface
{
    use ParserUtilTrait;
    const SHEET_NAME = 'videopublishers';
    const PUBLISHER = 'publisher';
    const NAME = 'name';
    const ID = 'id';

    /** @var  VideoPublisherManagerInterface */
    private $videoPublisherManager;

    /** @var  PublisherManagerInterface */
    private $publisherManager;

    /** @var  SymfonyStyle */
    private $io;

    /**
     * VideoPublisherImporter constructor.
     * @param VideoPublisherManagerInterface $videoPublisherManager
     * @param PublisherManagerInterface $publisherManager
     */
    public function __construct(VideoPublisherManagerInterface $videoPublisherManager, PublisherManagerInterface $publisherManager)
    {
        $this->videoPublisherManager = $videoPublisherManager;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @inheritdoc
     */
    public function importVideoPublishers($videoPublishers, $dryOption, SymfonyStyle $io)
    {
        $this->io = $io;

        $this->io->section(sprintf('Begin import %s video publishers to system', count($videoPublishers)));

        foreach ($videoPublishers as $videoPublisher) {
            if (!$videoPublisher instanceof VideoPublisherInterface) {
                continue;
            }

            if ($dryOption) {
                $this->io->note(sprintf('Dry Run: Prepare for video publisher: name = %s, id = %s', $videoPublisher->getName(), $videoPublisher->getId()));
                continue;
            }

            $this->videoPublisherManager->save($videoPublisher);

            $this->io->note(sprintf('Create/Update video publisher: name = %s, id = %s', $videoPublisher->getName(), $videoPublisher->getId()));
        }

        $this->io->success('Success import video publishers to system');

        return $videoPublishers;
    }

    /**
     * @inheritdoc
     */
    public function getVideoPublishersFromFileContents($contents, PublisherInterface $publisher)
    {
        $videoPublishers = [];
        if (!is_array($contents) || !array_key_exists(self::SHEET_NAME, $contents)) {
            return $videoPublishers;
        }

        $publisherArrayIds = $contents[self::SHEET_NAME];
        $headers = reset($publisherArrayIds);
        $headers = $this->normalizeColumns($headers);

        foreach ($publisherArrayIds as $key => $publisherInfo) {
            if (!is_array($publisherInfo) || $key == 0 || count($headers) != count($publisherInfo)) {
                continue;
            }

            $publisherInfo = array_combine($headers, $publisherInfo);
            $videoPublisher = $this->createVideoPublisherFromExcelRow($publisherInfo, $publisher);
            if (!$videoPublisher instanceof VideoPublisherInterface) {
                continue;
            }

            $videoPublishers[$videoPublisher->getName()] = $videoPublisher;
        }


        return $videoPublishers;
    }

    /**
     * @param $publisherInfo
     * @param PublisherInterface $publisher
     * @return array|mixed|null|VideoPublisher
     */
    private function createVideoPublisherFromExcelRow($publisherInfo, PublisherInterface $publisher)
    {
        $name = array_key_exists(self::NAME, $publisherInfo) ? $publisherInfo[self::NAME] : null;
        $publisherId = $publisher->getId();

        $videoPublisher = null;

        if (!empty($name)) {
            $videoPublisher = $this->videoPublisherManager->findByNameAndPublisherId($name, $publisherId);
            $videoPublisher = is_array($videoPublisher) ? reset($videoPublisher) : $videoPublisher;
        }

        if ($videoPublisher instanceof VideoPublisherInterface) {
            return $videoPublisher;
        }

        $videoPublisher = new VideoPublisher();
        $videoPublisher->setName($name);

        if (!empty($publisherId)) {
            $publisher = $this->publisherManager->find($publisherId);
            if ($publisher instanceof PublisherInterface) {
                $videoPublisher->setPublisher($publisher);
            }
        }

        return $videoPublisher;
    }
}