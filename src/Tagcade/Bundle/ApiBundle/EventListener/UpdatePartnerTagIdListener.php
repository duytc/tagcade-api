<?php


namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Service\Core\AdTag\PartnerTagIdMatcherInterface;

class UpdatePartnerTagIdListener
{
    /** @var PartnerTagIdMatcherInterface */
    protected $matcher;

    /**
     * UpdateAdTagHtmlListener constructor.
     * @param PartnerTagIdMatcherInterface $matcher
     */
    public function __construct(PartnerTagIdMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof LibraryAdTagInterface || ($entity instanceof LibraryAdTagInterface && !$args->hasChangedField('html'))) {
            return;
        }

        $this->updatePartnerTagId($entity);
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof AdTagInterface) {
            return;
        }

        $libraryAdTag = $entity->getLibraryAdTag();
        $libraryAdTag->addAdTag($entity);
        $this->updatePartnerTagId($libraryAdTag);
    }

    protected function updatePartnerTagId(LibraryAdTagInterface $libraryAdTag)
    {
        $partnerTagId = $this->matcher->extractPartnerTagId($libraryAdTag);

        if (is_string($partnerTagId) && strcmp($partnerTagId, $libraryAdTag->getPartnerTagId()) !== 0) {
            $libraryAdTag->setPartnerTagId($partnerTagId);
        }
    }
}