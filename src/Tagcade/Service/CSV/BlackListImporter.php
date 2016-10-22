<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\DomainManager\BlacklistManagerInterface;
use Tagcade\Entity\Core\Blacklist;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;

class BlackListImporter extends ListImporterAbstract implements BlackListImporterInterface
{
    use StringUtilTrait;

    const CSV_SEPARATOR = ',';
    const HEADER_POSITION = 0;

    const FILE_NAME = 'input.csv';
    const DOMAIN = 0;

    protected $HEADERS = ['domain'];

    /**
     * @var BlacklistManagerInterface
     */
    protected $blackListManager;

    public function __construct(DomainListManager $domainListManager, BlacklistManagerInterface $blackListManager, LoggerInterface $logger)
    {
        parent::__construct($domainListManager, $logger);
        $this->blackListManager = $blackListManager;
    }

    public function importCsv($filename, PublisherInterface $publisher, $name, $headerPosition = null, $csvSeparator = null)
    {
        $this->validateParameters($filename, $headerPosition, $csvSeparator);

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return;
        }

        $row = 0;
        $blackListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $this->logger->info(sprintf('start read row %d', $row), $data);
            if ($row <= $headerPosition) {
                $row++;
                continue;
            }

            $blackListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]));
            $blackListDomains[] = $blackListDomain;
            $this->logger->info(sprintf('finish read row %d', $row), $data);
            $row++;
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