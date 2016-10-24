<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\DomainManager\BlacklistManagerInterface;
use Tagcade\Entity\Core\Blacklist;
use Tagcade\Model\User\Role\PublisherInterface;

class BlackListImporter extends ListImporterAbstract implements BlackListImporterInterface
{
    /**
     * @var BlacklistManagerInterface
     */
    protected $blackListManager;

    public function __construct(DomainListManager $domainListManager, BlacklistManagerInterface $blackListManager, LoggerInterface $logger)
    {
        parent::__construct($domainListManager, $logger);
        $this->blackListManager = $blackListManager;
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

        $blackListDomains = [];
        $count = 0;
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $blackListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]), false);

            if ($blackListDomain === false) {
                $this->logger->info(sprintf('"%s" is not a valid domain', $data[0]));
                continue;
            }

            $blackListDomains[] = $blackListDomain;
            $count++;
        }

        if (empty($blackListDomains)) {
            return 0;
        }

        $blackList = new Blacklist();
        $blackList->setPublisher($publisher);
        $blackList->setName($name);
        $blackList->setDomains($blackListDomains);
        $this->blackListManager->save($blackList);

        $blackList->setSuffixKey($blackList->getId());
        $this->blackListManager->save($blackList);
        $this->domainListManager->saveBlacklist($blackList);

        return $count;
    }
}