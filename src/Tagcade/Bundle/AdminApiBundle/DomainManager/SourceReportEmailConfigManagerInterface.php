<?php

namespace Tagcade\Bundle\AdminApiBundle\DomainManager;


use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SourceReportEmailConfigManagerInterface {

    public function supportsEntity($entity);

    public function save(SourceReportEmailConfigInterface $emailConfig);

    public function delete(SourceReportEmailConfigInterface $emailConfig);

    public function createNew();

    public function find($id);

    public function all($limit = null, $offset = null);

    public function getSourceReportConfigForPublisher(PublisherInterface $publisher);

    /**
     * @param array $emails
     * @param SiteInterface[] $sites
     *
     * @throws InvalidArgumentException
     */
    public function saveSourceReportConfig(array $emails, array $sites);
} 