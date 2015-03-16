<?php

namespace Tagcade\Bundle\AdminApiBundle\Entity;

use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;

class SourceReportEmailConfig implements SourceReportEmailConfigInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var bool
     */
    protected $includedAll = false;

    /**
     * @var bool
     */
    protected $active = true;

    /**
     * @var SourceReportSiteConfigInterface[]
     */
    protected $sourceReportSiteConfigs;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIncludedAll()
    {
        return $this->includedAll;
    }

    /**
     * @param bool $includedAll
     * @return $this
     */
    public function setIncludedAll($includedAll)
    {
        $this->includedAll = $includedAll;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @inheritdoc
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return SourceReportSiteConfigInterface[]
     */
    public function getSourceReportSiteConfigs()
    {
        return $this->sourceReportSiteConfigs;
    }

    /**
     * @param SourceReportSiteConfigInterface[] $sourceReportSiteConfigs
     *
     * @return $this
     */
    public function setSourceReportSiteConfigs(array $sourceReportSiteConfigs)
    {
        $this->sourceReportSiteConfigs = $sourceReportSiteConfigs;

        return $this;
    }
}