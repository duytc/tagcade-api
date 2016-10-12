<?php


namespace Tagcade\Service\Core\VideoDemandAdTag;


interface AutoPauseServiceInterface
{
    /**
     * @param array $demandAdTags
     * @return mixed
     */
    public function autoPauseDemandAdTags(array $demandAdTags);

    /**
     * @param array $libraryDemandAdTags
     * @return mixed
     */
    public function autoPauseLibraryDemandAdTags(array $libraryDemandAdTags);
}