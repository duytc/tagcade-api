<?php

namespace Tagcade\Service\CSV;


use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
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
        if (!file_exists($filename) || !is_file($filename)) {
            throw new FileNotFoundException(sprintf('That file does not exists. Please recheck again this path %s', $filename));
        }

        $handle = fopen($filename, "r");
        if ($handle === FALSE) {
            return 0;
        }

        $count = 0;
        $whiteListDomains = [];
        while (($data = fgetcsv($handle, null, $csvSeparator)) !== FALSE) {
            $whiteListDomain = $this->extractDomain($this->adjustDomainPart($data[self::DOMAIN]), false);

            if ($whiteListDomain === false) {
                $this->logger->info(sprintf('"%s" is not a valid domain', $data[0]));
                continue;
            }

            $whiteListDomains[] = $whiteListDomain;
            $count++;
        }

        if (empty($whiteListDomains)) {
            return 0;
        }

        $whiteList = new WhiteList();
        $whiteList->setPublisher($publisher);
        $whiteList->setName($name);
        $whiteList->setDomains($whiteListDomains);
        $this->whiteListManager->save($whiteList);

        $whiteList->setSuffixKey($whiteList->getId());
        $this->whiteListManager->save($whiteList);
        $this->domainListManager->saveWhiteList($whiteList);

        return $count;
    }
}