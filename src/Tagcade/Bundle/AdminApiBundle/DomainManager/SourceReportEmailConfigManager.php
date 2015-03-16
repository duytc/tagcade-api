<?php

namespace Tagcade\Bundle\AdminApiBundle\DomainManager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Bundle\AdminApiBundle\Entity\SourceReportEmailConfig;
use Tagcade\Bundle\AdminApiBundle\Entity\SourceReportSiteConfig;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportSiteConfigInterface;
use Tagcade\Bundle\AdminApiBundle\Repository\SourceReportEmailConfigRepositoryInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class SourceReportEmailConfigManager implements SourceReportEmailConfigManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, SourceReportEmailConfigRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SourceReportEmailConfigInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(SourceReportEmailConfigInterface $emailConfig)
    {
        $this->om->persist($emailConfig);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(SourceReportEmailConfigInterface $emailConfig)
    {
        $this->om->remove($emailConfig);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new \ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }


    /**
     * @param PublisherInterface $publisher
     *
     * @return SourceReportEmailConfigInterface[]
     */
    public function getSourceReportConfigForPublisher(PublisherInterface $publisher)
    {
        return $this->repository->getSourceReportEmailConfigForPublisher($publisher);
    }


    /**
     * Save SourceReportConfig
     *
     * @param array $emails
     *
     * @param array $sites
     *
     * @throws InvalidArgumentException if sites|emails are null|empty or emails or emails invalid format
     */
    public function saveSourceReportConfig(array $emails, array $sites = null)
    {
        if (null === $emails || empty($emails) || $sites == null || empty($sites)) {
            throw new InvalidArgumentException('expect sites and emails');
        }

        //validate emails format
        foreach ($emails as $email) {
            $this->validateEmailFormat($email);
        }

        foreach ($emails as $email) {

            $emailConfigs = $this->repository->findBy(['email'=>$email]);

            $emailConfig = current($emailConfigs);

            // Create new email config
            if (!$emailConfig instanceof SourceReportEmailConfigInterface) {
                $emailConfig = new SourceReportEmailConfig();
                $emailConfig->setEmail($email);
            }

            // update site config for existing email
            /**
             * @var ArrayCollection $siteConfigs
             */
            $siteConfigs = $emailConfig->getSourceReportSiteConfigs();

            $siteConfigs = null === $siteConfigs ? [] : $siteConfigs->toArray();
            /**
             * @var array $siteConfigs
             */
            $existedSites = array_map(function (SourceReportSiteConfigInterface $siteConfig){
                return $siteConfig->getSite();
            }, $siteConfigs);

            foreach ($sites as $site) {

                if (!in_array($site, $existedSites)) {
                    $sourceReportConfig = new SourceReportSiteConfig();
                    $sourceReportConfig->setSourceReportEmailConfig($emailConfig);
                    $sourceReportConfig->setSite($site);

                    $siteConfigs[] = $sourceReportConfig;
                }
            }

            $emailConfig->setSourceReportSiteConfigs($siteConfigs);

            $this->om->persist($emailConfig);
        }

        $this->om->flush();
    }

    /**
     * Save SourceReportConfigIncludedAll
     *
     * @param array $emails
     *
     * @throws InvalidArgumentException if $emails null or format is invalid
     */
    public function saveSourceReportConfigIncludedAll(array $emails) {
        if (null === $emails) {
            throw new InvalidArgumentException('expect emails');
        }

        //validate emails format
        foreach ($emails as $email) {
            $this->validateEmailFormat($email);
        }

        //do save
        foreach ($emails as $email) {
            $emailConfigs = $this->repository->findBy(['email' => $email]);
            $emailConfig = current($emailConfigs);

            if (!$emailConfig instanceof SourceReportEmailConfigInterface) {
                /**
                 * @var SourceReportEmailConfigInterface $emailConfig
                 */
                $emailConfig = $this->createNew();
                $emailConfig->setEmail($email);
                $emailConfig->setIncludedAll(true);
            }

            $this->om->persist($emailConfig);

        }

        $this->om->flush();
    }

    /**
     * Validate email format
     *
     * @param string $email
     *
     * @throws InvalidArgumentException if format of $email is invalid
     */
    private function validateEmailFormat($email){
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format at ' . $email);
        }
    }
} 