<?php

namespace Tagcade\Bundle\AdminApiBundle\Model;


use Tagcade\Model\ModelInterface;

interface SourceReportEmailConfigInterface extends ModelInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getEmail();
    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return bool
     */
    public function getIncludedAll();

    /**
     * @param bool $includedAll
     * @return $this
     */
    public function setIncludedAll($includedAll);

    /**
     * @return SourceReportSiteConfigInterface[]
     */
    public function getSourceReportSiteConfigs();

    /**
     * @param SourceReportSiteConfigInterface[] $sourceReportSiteConfigs
     *
     * @return $this
     */
    public function setSourceReportSiteConfigs(array $sourceReportSiteConfigs);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $active
     */
    public function setActive($active);
} 