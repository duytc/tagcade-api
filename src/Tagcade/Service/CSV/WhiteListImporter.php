<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\DomainManager\WhiteListManagerInterface;
use Tagcade\Entity\Core\WhiteList;
use Tagcade\Model\User\Role\PublisherInterface;

class WhiteListImporter extends ListImporterAbstract implements WhiteListImporterInterface
{
    /**
     * @var WhiteListManagerInterface
     */
    protected $whiteListManager;

    public function __construct(DomainListManager $domainListManager, WhiteListManagerInterface $whiteListManager, LoggerInterface $logger)
    {
        parent::__construct($domainListManager, $logger);
        $this->whiteListManager = $whiteListManager;
    }

    public function importCsv($filename, PublisherInterface $publisher, $name, $csvSeparator = self::CSV_SEPARATOR)
    {
        $this->validateParameters($filename);

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return;
        }

        $row = 0;
        $whiteListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $this->logger->info(sprintf('Start read row %d', $row), $data);
            $whiteListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]), false);
            if ($whiteListDomain) {
                $this->logger->info(sprintf('Domain "%s" is valid', $data[0]));
                $whiteListDomains[] = $whiteListDomain;
            } else {
                $this->logger->info(sprintf('Domain "%s" is not valid', $data[0]));
            }
            $this->logger->info(sprintf('Finish read row %d', $row), $data);
            $row++;
        }

        if ($whiteListDomains) {
            $whiteList = new WhiteList();
            $whiteList->setPublisher($publisher);
            $whiteList->setName($name);
            $whiteList->setDomains($whiteListDomains);
            $this->whiteListManager->save($whiteList);
        } else {
            $this->logger->info(sprintf('There is no valid domain'));
            return;
        }

        $whiteList->setSuffixKey($whiteList->getId());
        $this->whiteListManager->save($whiteList);
        $this->domainListManager->saveWhiteList($whiteList);
    }
}