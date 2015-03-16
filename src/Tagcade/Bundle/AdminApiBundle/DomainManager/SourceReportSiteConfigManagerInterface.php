<?php
/**
 * Created by PhpStorm.
 * User: dunght163
 * Date: 11/03/2015
 * Time: 08:45
 */

namespace Tagcade\Bundle\AdminApiBundle\DomainManager;


use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;

interface SourceReportSiteConfigManagerInterface
{
    public function supportsEntity($entity);

    public function save(SourceReportSiteConfigInterface $siteConfig);

    public function delete(SourceReportSiteConfigInterface $siteConfig);

    public function createNew();

    public function find($id);

    public function all($limit = null, $offset = null);

    /**
     * save sourceReportConfig for emailConfig with sites
     *
     * @param SourceReportEmailConfigInterface $emailConfig
     * @param SiteInterface[] $sites
     * @throws InvalidArgumentException
     */
    public function saveSourceReportConfig(SourceReportEmailConfigInterface $emailConfig, array $sites);

}