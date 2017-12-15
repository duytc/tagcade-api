<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Behaviors\ParserUtilTrait;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\VideoDemandPartnerManagerInterface;
use Tagcade\Entity\Core\VideoDemandPartner;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class VideoDemandPartnerImporter implements VideoDemandPartnerImporterInterface
{
    use ParserUtilTrait;

    const SHEET_NAME = 'demandpartners';

    const PUBLISHER = 'videopublisher';
    const NAME = 'name';
    const IMPRESSION_CAP = 'impressioncap';
    const REQUEST_CAP = 'requestcap';

    /** @var  SymfonyStyle */
    private $io;

    /** @var VideoDemandPartnerManagerInterface */
    private $videoDemandPartnerManager;

    /** @var  PublisherManagerInterface */
    private $publisherManager;

    /**
     * VideoPublisherImporter constructor.
     * @param VideoDemandPartnerManagerInterface $videoDemandPartnerManager
     * @param PublisherManagerInterface $publisherManager
     */
    public function __construct(VideoDemandPartnerManagerInterface $videoDemandPartnerManager, PublisherManagerInterface $publisherManager)
    {
        $this->videoDemandPartnerManager = $videoDemandPartnerManager;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @inheritdoc
     */
    public function importVideoDemandPartners($videoDemandPartners, $dryOption, SymfonyStyle $io, $videoPublishers)
    {
        $this->io = $io;

        $this->io->section(sprintf('Begin import %s video demand partners to system', count($videoDemandPartners)));

        foreach ($videoDemandPartners as $videoDemandPartner) {
            if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
                continue;
            }

            if ($dryOption) {
                $this->io->note(sprintf('Dry Run: Prepare for video demand partner: name = %s', $videoDemandPartner->getName()));
                continue;
            }

            $this->videoDemandPartnerManager->save($videoDemandPartner);
            $this->io->note(sprintf('Create/Update video demand partner: name = %s, id = %s', $videoDemandPartner->getName(), $videoDemandPartner->getId()));
        }

        $this->io->success('Success import video demand partners to system');

        return $videoDemandPartners;
    }

    /**
     * @inheritdoc
     */
    public function getVideoDemandPartnersFromFileContents($contents, $overwrite, $videoPublishers)
    {
        $videoDemandPartners = [];
        if (!is_array($contents) || !array_key_exists(self::SHEET_NAME, $contents)) {
            return $videoDemandPartners;
        }

        $videoDemandPartnerArray = $contents[self::SHEET_NAME];
        $headers = reset($videoDemandPartnerArray);
        $headers = $this->normalizeColumns($headers);

        foreach ($videoDemandPartnerArray as $key => $videoDemandPartnerInfo) {
            if (!is_array($videoDemandPartnerInfo) || $key == 0 || count($headers) != count($videoDemandPartnerInfo)) {
                continue;
            }

            $videoDemandPartnerInfo = array_combine($headers, $videoDemandPartnerInfo);
            $videoDemandPartner = $this->createVideoDemandPartnerDataFromExcelRow($videoDemandPartnerInfo, $overwrite, $videoPublishers);
            if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
                continue;
            }

            $videoDemandPartners[$videoDemandPartner->getName()] = $videoDemandPartner;
        }

        return $videoDemandPartners;
    }

    /**
     * @param $excelRow
     * @param $overwrite
     * @param $videoPublishers
     * @return array|mixed|null|VideoDemandPartner|\Tagcade\Model\ModelInterface
     */
    private function createVideoDemandPartnerDataFromExcelRow($excelRow, $overwrite, $videoPublishers)
    {
        $name = array_key_exists(self::NAME, $excelRow) ? $excelRow[self::NAME] : null;
        $requestCap = array_key_exists(self::REQUEST_CAP, $excelRow) ? $excelRow[self::REQUEST_CAP] : null;
        $impressionCap = array_key_exists(self::IMPRESSION_CAP, $excelRow) ? $excelRow[self::IMPRESSION_CAP] : null;
        $videoPublisherPos = array_key_exists(self::PUBLISHER, $excelRow) ? $excelRow[self::PUBLISHER] : null;
        $videoPublisher = array_key_exists($videoPublisherPos, $videoPublishers) ? $videoPublishers[$videoPublisherPos] : null;

        if (empty($name) ||
            !$videoPublisher instanceof VideoPublisherInterface ||
            !$videoPublisher->getPublisher() instanceof PublisherInterface) {
            return null;
        }

        $publisher = $videoPublisher->getPublisher();

        $videoDemandPartner = $this->videoDemandPartnerManager->findByNameAndPublisher($name, $publisher);
        $videoDemandPartner = is_array($videoDemandPartner) ? reset($videoDemandPartner) : $videoDemandPartner;

        if ($videoDemandPartner instanceof VideoDemandPartnerInterface) {
            if ($overwrite) {
                $videoDemandPartner
                    ->setPublisher($publisher)
                    ->setName($name)
                    ->setRequestCap($requestCap)
                    ->setImpressionCap($impressionCap);
            }

            return $videoDemandPartner;
        }

        $videoDemandPartner = new VideoDemandPartner();

        $videoDemandPartner
            ->setPublisher($publisher)
            ->setName($name)
            ->setRequestCap($requestCap)
            ->setImpressionCap($impressionCap);

        return $videoDemandPartner;
    }
}