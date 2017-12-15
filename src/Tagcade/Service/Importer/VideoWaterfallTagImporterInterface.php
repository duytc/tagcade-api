<?php


namespace Tagcade\Service\Importer;

use Symfony\Component\Console\Style\SymfonyStyle;

interface VideoWaterfallTagImporterInterface
{
    /**
     * @param $videoWaterfallTags
     * @param $dryOption
     * @param SymfonyStyle $io
     * @param $videoPublishers
     * @return mixed
     */
    public function importVideoWaterfallTags($videoWaterfallTags, $dryOption, SymfonyStyle $io, $videoPublishers);

    /**
     * @param $excelRows
     * @param $overwrite
     * @param $videoPublishers
     * @return array
     */
    public function getVideoWaterfallTagsFromFileContents($excelRows, $overwrite, $videoPublishers);
}