<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Cache\V2\DisplayBlacklistCacheManager;
use Tagcade\DomainManager\DisplayBlacklistManagerInterface;
use Tagcade\Entity\Core\DisplayBlacklist;
use Tagcade\Model\User\Role\PublisherInterface;

class DisplayBlackListImporter extends DisplayListImporterAbstract implements BlackListImporterInterface
{
    /**
     * @var DisplayBlackListManagerInterface
     */
    protected $displayBlackListManager;

    public function __construct(DisplayBlacklistCacheManager $displayBlackListCacheManager, DisplayBlacklistManagerInterface $displayBlackListManager, LoggerInterface $logger)
    {
        parent::__construct($displayBlackListCacheManager, $logger);
        $this->displayBlackListManager = $displayBlackListManager;
    }

    public function importCsv($filename, PublisherInterface $publisher, $name, $csvSeparator = self::CSV_SEPARATOR)
    {
        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return 0;
        }

        $displayBlackListDomains = [];
        $count = 0;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $DisplayBlackListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]), false);

            if ($DisplayBlackListDomain === false) {
                $this->logger->info(sprintf('"%s" is not a valid domain', $data[0]));
                continue;
            }

            $displayBlackListDomains[] = $DisplayBlackListDomain;
            $count++;
        }

        $displayBlackList = $this->displayBlackListManager->getDisplayBlacklistsByNameForPublisher($publisher, $name);
        if ($displayBlackList) {
            throw new \Exception(sprintf("%s is exist in black list name", $name));
        }

        if (empty($displayBlackListDomains)) {
            return 0;
        }

        $displayBlackListDomains = array_values(array_unique($displayBlackListDomains));
        $displayBlackList = new DisplayBlacklist();
        $displayBlackList->setName($name);
        $displayBlackList->setDomains($displayBlackListDomains);
        $displayBlackList->setPublisher($publisher);

        $this->displayBlackListManager->save($displayBlackList);
        $this->displayBlacklistCacheManager->saveBlacklist($displayBlackList);

        return $count;
    }
}