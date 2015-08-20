<?php

namespace Tagcade\Bundle\AdminApiBundle\Repository;


use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SourceReportEmailConfigRepositoryInterface extends ObjectRepository
{

    /**
     * Get source report config for publisher
     *
     * @param PublisherInterface $publisher
     *
     * @return SourceReportEmailConfigInterface[]
     *
     */
    public function getSourceReportEmailConfigForPublisher(PublisherInterface $publisher);

    /**
     * Get all active email config
     *
     * @return SourceReportEmailConfigInterface[]
     */
    public function getActiveConfig();
}