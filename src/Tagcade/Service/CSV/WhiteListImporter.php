<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Tagcade\Cache\Video\DomainListManager;
use Tagcade\DomainManager\WhiteListManagerInterface;
use Tagcade\Entity\Core\WhiteList;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\StringUtilTrait;

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

    public function importCsv($filename, PublisherInterface $publisher, $name, $headerPosition = null, $csvSeparator = null)
    {
        $this->validateParameters($filename, $headerPosition, $csvSeparator);

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return;
        }

        $row = 0;
        $whiteListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $this->logger->info(sprintf('start read row %d', $row), $data);
            if ($row <= $headerPosition) {
                $row++;
                continue;
            }

            $whiteListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]));
            $whiteListDomains[] = $whiteListDomain;
            $this->logger->info(sprintf('finish read row %d', $row), $data);
            $row++;
        }

        $whiteList = new WhiteList();
        $whiteList->setPublisher($publisher);
        $whiteList->setName($name);
        $whiteList->setDomains($whiteListDomains);
        $this->whiteListManager->save($whiteList);

        $whiteList->setSuffixKey($whiteList->getId());
        $this->whiteListManager->save($whiteList);
        $this->domainListManager->saveWhiteList($whiteList);
    }
}