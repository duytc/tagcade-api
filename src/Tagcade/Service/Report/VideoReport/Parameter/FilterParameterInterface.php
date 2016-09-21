<?php


namespace Tagcade\Service\Report\VideoReport\Parameter;


interface FilterParameterInterface {

    /**
     * @return mixed
     */
    public function getEndDate();

    /**
     * @return mixed
     */
    public function getPublishers();

    /**
     * @return mixed
     */
    public function getVideoPublishers();

    /**
     * @return mixed
     */
    public function getStartDate();

    /**
     * @return mixed
     */
    public function getVideoDemandAdTags();

    /**
     * @return mixed
     */
    public function getVideoWaterfallTags();

    /**
     * @return mixed
     */
    public function getVideoDemandPartners();


    public function setPublisherId(array $publisherIds);

} 