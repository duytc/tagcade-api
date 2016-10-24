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
            return;
        }

        $row = 0;
        $blackListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $this->logger->info(sprintf('Start read row %d', $row), $data);
            $blackListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]), false);
            if ($blackListDomain) {
                $this->logger->info(sprintf('Domain "%s" is valid', $data[0]));
                $blackListDomains[] = $blackListDomain;
            } else {
                $this->logger->info(sprintf('Domain "%s" is not valid', $data[0]));
            }
            $this->logger->info(sprintf('Finish read row %d', $row), $data);
            $row++;
        }

        if (empty($blackListDomains)) {
            $this->logger->info(sprintf('There is no valid domain'));
            return;
        }

        $blackList = new Blacklist();
        $blackList->setPublisher($publisher);
        $blackList->setName($name);
        $blackList->setDomains($blackListDomains);
        $this->blackListManager->save($blackList);

        $blackList->setSuffixKey($blackList->getId());
        $this->blackListManager->save($blackList);
        $this->domainListManager->saveBlacklist($blackList);
    }
}