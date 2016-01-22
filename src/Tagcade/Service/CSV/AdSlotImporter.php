<?php
namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;
use Tagcade\Service\TagGenerator;

class AdSlotImporter implements AdSlotImporterInterface
{
    use StringUtilTrait;

    const EMAIL_PATTERN = '/[A-Za-z0-9_-]+@[A-Za-z0-9_-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/';

    const OUTPUT_FILE_NAME = 'output.csv';
    const CSV_SEPARATOR = ',';
    const HEADER_POSITION = 0;

    const SLOT_KEY = 'slot';
    const SITE_KEY = 'site';
    const HTML_CODE_KEY = 'html';
    const OUTPUT_FILE_KEY = 'output';

    const SITE_NAME = 1;
    const SITE_DOMAIN = 2;

    const PUBLISHER_EMAIL = 13;

    const AD_SLOT_1_NAME = 3;
    const AD_SLOT_1_WIDTH = 4;
    const AD_SLOT_1_HEIGHT = 5;

    const AD_SLOT_2_NAME = 6;
    const AD_SLOT_2_WIDTH = 7;
    const AD_SLOT_2_HEIGHT = 8;

    const AD_SLOT_3_NAME = 9;
    const AD_SLOT_3_WIDTH = 10;
    const AD_SLOT_3_HEIGHT = 11;

    const AD_SLOT_1_HTML_CODE_POSITION = 6;
    const AD_SLOT_2_HTML_CODE_POSITION = 10;
    const AD_SLOT_3_HTML_CODE_POSITION = 14;

    const AD_SLOT_1_INDEX = 0;
    const AD_SLOT_2_INDEX = 1;
    const AD_SLOT_3_INDEX = 2;

    const MIN_NUM_COLS = 14;

    protected $HEADERS_FOR_JS_TAGS = ['Ad Slot Html Code 1', 'Ad Slot Html Code 2', 'Ad Slot Html Code 3'];
    protected $HEADERS = ['region', 'site name', 'site url', 'ad slot name 1', 'ad slot width 1', 'ad slot height 1', 'ad slot name 2', 'ad slot width 2', 'ad slot height 2', 'ad slot name 3', 'ad slot width 3', 'ad slot height 3', 'publisher contact name', 'publisher email'];
    protected $OUTPUT_FILE_HEADERS = ['Region', 'Site Name', 'Site URL', 'Ad Slot Name 1', 'Ad Slot Width 1', 'Ad Slot Height 1', 'Ad Slot Html Code 1', 'Ad Slot Name 2', 'Ad Slot Width 2', 'Ad Slot Height 2', 'Ad Slot Html Code 2', 'Ad Slot Name 3', 'Ad Slot Width 3', 'Ad Slot Height 3', 'Ad Slot Html Code 3', 'Publisher Contact Name', 'Publisher Email'];
    /**
     * @var SiteManagerInterface
     */
    private $siteManager;
    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;
    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;
    /**
     * @var TagGenerator
     */
    private $tagGenerator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PublisherManagerInterface $publisherManager
     * @param SiteManagerInterface $siteManager
     * @param DisplayAdSlotManagerInterface $adSlotManager
     * @param TagGenerator $tagGenerator
     */
    public function __construct(PublisherManagerInterface $publisherManager, SiteManagerInterface $siteManager, DisplayAdSlotManagerInterface $adSlotManager, TagGenerator $tagGenerator, LoggerInterface $logger)
    {
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->publisherManager = $publisherManager;
        $this->tagGenerator = $tagGenerator;
        $this->logger = $logger;
    }

    /**
     * @param $filename
     * @param $headerPosition
     * @param $outputFileName
     * @param string $csvSeparator
     * @return array
     */
    public function importCsv($filename, $headerPosition, $outputFileName, $csvSeparator)
    {
        if ($headerPosition === null) {
            $headerPosition = self::HEADER_POSITION;
        }

        if ($outputFileName === null) {
            $outputFileName = self::OUTPUT_FILE_NAME;
        }

        if ($csvSeparator === null) {
            $csvSeparator = self::CSV_SEPARATOR;
        }

        $siteCount = 0;
        $adSlotCount = 0;

        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $valid = $this->checkCSVFileFormat($filename, $this->HEADERS, $headerPosition, $csvSeparator);

        if (!$valid) {
            throw new InvalidArgumentException('The file format is invalid');
        }

        $handle = fopen($filename, "r");
        if ($handle === false) {
            return array (
                self::SITE_KEY => $siteCount,
                self::SLOT_KEY => $adSlotCount,
                self::OUTPUT_FILE_KEY => ''
            );
        }

        $row = 0;
        $newRows[] = $this->OUTPUT_FILE_HEADERS;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            // check header is matched
            if ($row <= $headerPosition) {
                $row++;
                continue;
            }

            if (count($data) < self::MIN_NUM_COLS) {
                fclose($handle);
                throw new InvalidArgumentException('The file format is invalid');
            }

            $this->logger->info(sprintf('importing row %d', $row), $data);

            $res = $this->importSingleLine($data);

            $this->logger->info(sprintf('finish importing row %d', $row));

            $siteCount += $res[self::SITE_KEY];
            $adSlotCount += $res[self::SLOT_KEY];

            // insert html code into $data at right position
            $dataToReplace = ['', '', ''];
            if (is_array($res[self::HTML_CODE_KEY])) {
                $dataToReplace = $res[self::HTML_CODE_KEY];
            }

            array_splice($data, self::AD_SLOT_1_HTML_CODE_POSITION, 0, $dataToReplace[self::AD_SLOT_1_INDEX]);
            array_splice($data, self::AD_SLOT_2_HTML_CODE_POSITION, 0, $dataToReplace[self::AD_SLOT_2_INDEX]);
            array_splice($data, self::AD_SLOT_3_HTML_CODE_POSITION, 0, $dataToReplace[self::AD_SLOT_3_INDEX]);

            $newRows[] = $data;
        }

        fclose($handle);

        $info = pathinfo($filename);
        $outputFilePath = $info['dirname'] . '/' . $outputFileName;

        $output = $this->generateOutputCsv($outputFilePath, $newRows, $csvSeparator);

        if (!$output) {
            throw new RuntimeException('Some error occurs when creating output file');
        }

        return array (
            self::SITE_KEY => $siteCount,
            self::SLOT_KEY => $adSlotCount,
            self::OUTPUT_FILE_KEY => $outputFilePath
        );
    }

    /**
     * @param $filename
     * @param array $rows
     * @param string $csvSeparator
     * @return bool
     */
    private function generateOutputCsv($filename, array $rows, $csvSeparator = ',')
    {
        $handle = fopen($filename, 'w');
        if ($handle === FALSE) {
            return false;
        }

        foreach ($rows as $row) {
            fputcsv($handle, $row, $csvSeparator);
        }

        fclose($handle);
        return true;
    }

    /**
     * @param $cells
     * @return bool
     */
    protected function importSingleLine(array $cells)
    {
        $siteCount = 0;
        $adSlotCount = 0;
        $siteDomain = $this->extractDomain($this->adjustDomainPart($cells[self::SITE_DOMAIN]));

        $emails = $this->extractEmailsFromString($cells[self::PUBLISHER_EMAIL]);
        $publisher = $this->findPublisherFromMultipleEmail($emails);

        if (!$publisher instanceof PublisherInterface) {
            return array (
                self::SLOT_KEY => 0,
                self::SITE_KEY => 0,
                self::HTML_CODE_KEY => ''
            );
        }

        $sites = $this->siteManager->getSiteByDomainAndPublisher($siteDomain, $publisher);
        if (count($sites) >= 1) {
            $site = $sites[0];
            $this->adSlotManager->deleteAdSlotForSite($site);
        }
        else {
            $site = new Site();
            $site->setName($cells[self::SITE_NAME]);
            $site->setDomain(strtolower($siteDomain));
            $site->setPublisher($publisher);
            $site->setEnableSourceReport(true);
            $site->setAutoCreate(false);
            $siteCount++;
            $this->siteManager->save($site);
        }

        // save JsTags of all processing ad slots
        $jsTags[] = $this->saveSingleAdSlot($cells[self::AD_SLOT_1_NAME], $cells[self::AD_SLOT_1_WIDTH], $cells[self::AD_SLOT_1_HEIGHT], $site, $publisher, $adSlotCount);
        $jsTags[] = $this->saveSingleAdSlot($cells[self::AD_SLOT_2_NAME], $cells[self::AD_SLOT_2_WIDTH], $cells[self::AD_SLOT_2_HEIGHT], $site, $publisher, $adSlotCount);
        $jsTags[] = $this->saveSingleAdSlot($cells[self::AD_SLOT_3_NAME], $cells[self::AD_SLOT_3_WIDTH], $cells[self::AD_SLOT_3_HEIGHT], $site, $publisher, $adSlotCount);

        return array (
            self::SITE_KEY => $siteCount,
            self::SLOT_KEY => $adSlotCount,
            self::HTML_CODE_KEY => $jsTags
        );
    }

    /**
     * Check if there's an ad slot with given name, if yes delete it then create a new one
     * @param $name
     * @param $width
     * @param $height
     * @param SiteInterface $site
     * @param PublisherInterface $publisher
     * @param $adSlotCount
     * @return String JsTag of the processing ad slot
     */
    private function saveSingleAdSlot($name, $width, $height, SiteInterface $site, PublisherInterface $publisher, &$adSlotCount)
    {
        // ad slot with empty name will not be inserted
        if (empty($name)) {
            return '';
        }

        $adSlot = $this->adSlotManager->getAdSlotForSiteByName($site, $name);

        try {

            if ($adSlot instanceof BaseAdSlotInterface) {
                return '';
            }

            $adSlot = new DisplayAdSlot();

            $libraryAdSlot1 = new LibraryDisplayAdSlot();
            $libraryAdSlot1->setHeight(intval($height));
            $libraryAdSlot1->setWidth(intval($width));
            $libraryAdSlot1->setPublisher($publisher);
            $libraryAdSlot1->setName($name);
            $libraryAdSlot1->setAutoFit(true);
            $libraryAdSlot1->setPassbackMode('position');
            $libraryAdSlot1->setType('display');
            $libraryAdSlot1->setVisible(false);

            $adSlot->setLibraryAdSlot($libraryAdSlot1);
            $adSlot->setSite($site);
            $adSlot->setAutoCreate(false);

            $adSlotCount++;
            $this->adSlotManager->save($adSlot);

            return $this->tagGenerator->createJsTags($adSlot);
        }
        catch(\Exception $ex) {
            return '';
        }
    }

    /**
     * Check if the given CSV file has the correct format
     * @param $filename
     * @param array $headers
     * @param $headerRow
     * @param $csvSeparator
     * @return bool
     */
    private function checkCSVFileFormat($filename, array $headers, $headerRow, $csvSeparator = ',')
    {
        $handle = fopen($filename, "r");

        if ($handle === FALSE) {
            return FALSE;
        }

        $row = 0;
        $matched = false;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            // check header is matched
            if ($row === $headerRow) {
                $matched = $this->matchRow($data, $headers);
                break;
            }

            $row ++;
        }
        fclose($handle);

        return $matched;
    }

    /**
     * check if a rowData and a needleData are matched. Also convert rowData to ASCII Encoding before comparing
     * @param array $rowData row data read from file
     * @param array $headers expected data to compare
     * @return bool
     */
    private function matchRow(array $rowData, array $headers)
    {
        $rowDataCount = count($rowData);
        $headerCount = count($headers);

        if ($rowDataCount < $headerCount) {
            return false;
        }

        if ($rowDataCount > $headerCount) {
            $rowData = array_slice($rowData, 0, $headerCount);
        }

        // canonical data before compare
        $matched = true;
        for($i = 0; $i < $headerCount; $i ++) {
            $rowItem = trim($rowData[$i]);
            $headerItem = trim($headers[$i]);

            $rowItem = str_replace('  ',' ', $rowItem);
            $rowItem = str_replace(' ','-', $rowItem);
            $headerDataItem =  str_replace(' ','-', $headerItem);

            if (strcasecmp($rowItem, $headerDataItem) != 0) {
                $matched = false;
                break;
            }

        }

        return $matched;
    }

    /**
     * Remove any non-ASCII characters and convert known non-ASCII characters
     * to their ASCII equivalents, if possible.
     *
     * @param string $string
     * @return string $string
     * @author Jay Williams <myd3.com>
     * @license MIT License
     * @link http://gist.github.com/119517
     */
    private function convert_ascii($string)
    {
        // Replace Single Curly Quotes
        $search[] = chr(226) . chr(128) . chr(152);
        $replace[] = "'";
        $search[] = chr(226) . chr(128) . chr(153);
        $replace[] = "'";
        // Replace Smart Double Curly Quotes
        $search[] = chr(226) . chr(128) . chr(156);
        $replace[] = '"';
        $search[] = chr(226) . chr(128) . chr(157);
        $replace[] = '"';
        // Replace En Dash
        $search[] = chr(226) . chr(128) . chr(147);
        $replace[] = '--';
        // Replace Em Dash
        $search[] = chr(226) . chr(128) . chr(148);
        $replace[] = '---';
        // Replace Bullet
        $search[] = chr(226) . chr(128) . chr(162);
        $replace[] = '*';
        // Replace Middle Dot
        $search[] = chr(194) . chr(183);
        $replace[] = '*';
        // Replace Ellipsis with three consecutive dots
        $search[] = chr(226) . chr(128) . chr(166);
        $replace[] = '...';
        // Apply Replacements
        $string = str_replace($search, $replace, $string);
        // Remove any non-ASCII Characters
        $string = preg_replace("/[^\x01-\x7F]/", "", $string);
        return $string;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return bool
     */
    protected function arrayCompare(array $array1, array $array2)
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        return (!array_diff($array1, $array2) && !array_diff($array2, $array1));
    }

    /**
     * @param $value
     * @return mixed
     */
    private function adjustDomainPart($value)
    {
        return explode(' ', $value)[0];
    }

    /**
     * find publisher by the given list of emails. null returned if no publisher found.
     * @param array $emails
     * @return \FOS\UserBundle\Model\UserInterface|null
     */
    protected function findPublisherFromMultipleEmail(array $emails)
    {
        if (count($emails) < 1) {
            return null;
        }

        foreach($emails as $email) {
            $publisher = $this->publisherManager->findUserByUsernameOrEmail($email);
            if ($publisher instanceof PublisherInterface) {
                return $publisher;
            }
        }

        return null;
    }

    /**
     * get all valid email from a string
     * @param $str
     * @return mixed
     */
    protected function extractEmailsFromString($str)
    {
        $emails = array();
        preg_match_all(self::EMAIL_PATTERN, $str, $emails);

        return $emails[0];
    }
}