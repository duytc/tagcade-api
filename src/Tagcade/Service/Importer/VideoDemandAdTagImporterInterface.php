<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

interface VideoDemandAdTagImporterInterface
{
    /**
     * @param $videoDemandAdTags
     * @param $dryOption
     * @param SymfonyStyle $io
     * @return mixed
     */
    public function importVideoDemandAdTags($videoDemandAdTags, $dryOption, SymfonyStyle $io);

    /**
     * @param $excelRows
     * @param $overwrite
     * @param $videoDemandPartners
     * @param $videoWaterfallTags
     * @return array
     */
    public function getVideoDemandAdTagsFromFileContents($excelRows, $overwrite, $videoDemandPartners, $videoWaterfallTags);
}