<?php


namespace Tagcade\Service\CSV;


use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\DisplayAdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\LibraryDisplayAdSlot;
use Tagcade\Entity\Core\Site;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;

class AdSlotImporter implements AdSlotImporterInterface
{
    use StringUtilTrait;

    const HEADER_ROW = 1;
    protected $HEADERS = ['region', 'site name', 'site url', 'ad slot name 1', 'ad slot width 1', 'ad slot height 1', 'ad slot name 2', 'ad slot width 2', 'ad slot height 2', 'ad slot name 3', 'ad slot width 3', 'ad slot height 3', 'publisher contact name', 'publisher email'];
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
     * @param PublisherManagerInterface $publisherManager
     * @param SiteManagerInterface $siteManager
     * @param DisplayAdSlotManagerInterface $adSlotManager
     */
    public function __construct(PublisherManagerInterface $publisherManager, SiteManagerInterface $siteManager, DisplayAdSlotManagerInterface $adSlotManager)
    {
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @param $filename
     * @return bool
     */
    public function import($filename)
    {
        $siteCount = 0;
        $adSlotCount = 0;

        if (!file_exists($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $valid = $this->checkCSVFileFormat($filename, $this->HEADERS, self::HEADER_ROW);

        if ($valid === false) {
            throw new InvalidArgumentException('The file format is invalid');
        }

        if (($handle = fopen($filename, "r")) !== FALSE) {
            $row = 1;

            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                // check header is matched
                if ($row === self::HEADER_ROW) {
                    $row++;
                    continue;
                }

                $res = $this->importSingleLine($data);
                $siteCount += $res['site'];
                $adSlotCount += $res['slot'];
            }

            fclose($handle);
            return array(
                'site' => $siteCount,
                'slot' => $adSlotCount
            );
        }

        fclose($handle);
        return array(
            'site' => $siteCount,
            'slot' => $adSlotCount
        );
    }

    /**
     * @param $line
     * @return bool
     */
    protected function importSingleLine($line)
    {
        $siteCount = 0;
        $adSlotCount = 0;
        $siteDomain = $this->extractDomain($this->adjustDomainPart($line[2]));
        $publisher = $this->publisherManager->findUserByUsernameOrEmail($line[13]);
        if (!$publisher instanceof PublisherInterface) {
            return true;
        }

        $sites = $this->siteManager->getSiteByDomainAndPublisher($siteDomain, $publisher);
        if (count($sites) < 1) {
            $site = new Site();
            $site->setName($line[1]);
            $site->setDomain(strtolower($siteDomain));
            $site->setPublisher($publisher);
            $site->setEnableSourceReport(true);
            $site->setAutoCreate(false);
            $siteCount++;
            $this->siteManager->save($site);
        }

        if ($this->saveSingleAdSlot($line[3], $line[4], $line[5], $sites[0], $publisher)) {
            $adSlotCount++;
        }

        if ($this->saveSingleAdSlot($line[6], $line[7], $line[8], $sites[0], $publisher)) {
            $adSlotCount++;
        }

        if ($this->saveSingleAdSlot($line[9], $line[10], $line[11], $sites[0], $publisher)) {
            $adSlotCount++;
        }

        return array(
            'site' => $siteCount,
            'slot' => $adSlotCount
        );
    }

    /**
     * @param $name
     * @param $width
     * @param $height
     * @param SiteInterface $site
     * @param PublisherInterface $publisher
     * @return bool
     */
    private function saveSingleAdSlot($name, $width, $height, SiteInterface $site, PublisherInterface $publisher)
    {
        $inserted = false;
        $adSlot = $this->adSlotManager->getAdSlotForSiteByName($site, $name);

        try {
            if (!$adSlot instanceof BaseAdSlotInterface) {
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
                $inserted = true;
            }
            else {
                $adSlot->setName($name);
                $adSlot->setSite($site);
                $adSlot->setWidth(intval($width));
                $adSlot->setHeight(intval($height));
                $adSlot->setAutoCreate(false);
                $adSlot->setAutoFit(false);
                $adSlot->setPassbackMode('position');
            }

            $this->adSlotManager->save($adSlot);
        }
        catch(\Exception $ex) {
            return false;
        }

        return $inserted;
    }

    /**
     * @param $filename
     * @param array $headers
     * @param $headerRow
     * @return bool
     */
    public function checkCSVFileFormat($filename, array $headers, $headerRow)
    {
        if (($handle = fopen($filename, "r")) !== FALSE) {
            $row = 1;
            $matched = false;

            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                // check header is matched
                if ($row === $headerRow) {
                    $matched = $this->matchRow($data, $headers);
                    break;
                }
            }

            fclose($handle);
            return $matched;
        }

        fclose($handle);
        return false;
    }

    /**
     * check if a rowData and a needleData are matched. Also convert rowData to ASCII Encoding before comparing
     * @param array $rowData row data read from file
     * @param array $needleData expected data to compare
     * @return bool
     */
    private function matchRow(array $rowData, array $needleData)
    {
        $rowData = array_map(function ($val) {
            return trim(strtolower($val));
        }, $rowData);

        $rowData = array_filter($rowData, function ($val) {
            return !empty($val);
        });

        $rowData = $this->convertEncodingToASCII($rowData);

        return $this->arrayCompare($rowData, $needleData);
    }

    /**
     * convert a string To ASCII Encoding
     * @param array $data
     * @return array
     */
    private function convertEncodingToASCII(array $data)
    {
        foreach ($data as &$item) {
            if (!mb_check_encoding($item, 'ASCII')) {
                $item = $this->convert_ascii($item);
            }
        }

        return $data;
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
}