<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Model\Core\LibraryAdTagInterface;

class UpdateAdTagHtmlListener {

    const AD_TAG_TYPE_IMAGE = 1;
    const AD_TAG_TYPE_CUSTOM = 0;

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof LibraryAdTagInterface) {
            return;
        }

        $this->updateTagHtml($entity);
    }

    /**
     * handle event postPersist one site, this auto add site to SourceReportSiteConfig & SourceReportEmailConfig.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(!$entity instanceof LibraryAdTagInterface) {
            return;
        }

        $this->updateTagHtml($entity);
    }

    protected function updateTagHtml(LibraryAdTagInterface $libraryAdTag)
    {
        $html = $this->createHtmlFromDescriptor($libraryAdTag);
        $libraryAdTag->setHtml($html);
    }

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     *
     * @return string
     */
    protected function createHtmlFromDescriptor(LibraryAdTagInterface $libraryAdTag)
    {
        switch ($libraryAdTag->getAdType()) {
            case self::AD_TAG_TYPE_IMAGE:
                return $this->createImageAdTag($libraryAdTag->getDescriptor());
            default:
                break;

        }

        return $libraryAdTag->getHtml();
    }

    /**
     * @param array $descriptor
     *
     * @return string
     */
    protected function createImageAdTag(array $descriptor)
    {
        $imageUrl = $descriptor['imageUrl'];
        $targetUrl = $descriptor['targetUrl'];

        return '<a href="' . $targetUrl . '" target="_blank"><img src="' . $imageUrl . '" /></a>';
    }
}