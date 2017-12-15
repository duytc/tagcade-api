<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoPublisherImporterInterface
{
    /**
     * @param $videoPublishers
     * @param $dryOption
     * @param SymfonyStyle $io
     * @return mixed
     */
    public function importVideoPublishers($videoPublishers, $dryOption, SymfonyStyle $io);

    /**
     * @param $contents
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getVideoPublishersFromFileContents($contents, PublisherInterface $publisher);
}