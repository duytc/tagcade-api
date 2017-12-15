<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

interface VideoDemandPartnerImporterInterface
{
    /**
     * @param $videoDemandPartners
     * @param $dryOption
     * @param SymfonyStyle $io
     * @param $videoPublishers
     * @return mixed
     */
    public function importVideoDemandPartners($videoDemandPartners, $dryOption, SymfonyStyle $io, $videoPublishers);

    /**
     * @param $contents
     * @param $overwrite
     * @param $videoPublishers
     * @return mixed
     */
    public function getVideoDemandPartnersFromFileContents($contents, $overwrite, $videoPublishers);
}