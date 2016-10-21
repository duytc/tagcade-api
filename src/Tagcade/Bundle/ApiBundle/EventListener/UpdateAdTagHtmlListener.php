<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Tagcade\Entity\Core\LibraryAdTag;
use Tagcade\Model\Core\LibraryAdTagInterface;

class UpdateAdTagHtmlListener
{
    private $inBannerVideoJsUrl;

    public function __construct($inBannerVideoJsUrl)
    {
        $this->inBannerVideoJsUrl = $inBannerVideoJsUrl;
    }


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
            case LibraryAdTag::AD_TYPE_IMAGE:
                return $this->createImageAdTag($libraryAdTag->getDescriptor());
            case LibraryAdTag::AD_TYPE_IN_BANNER:
                return $this->createInBannerHtml($libraryAdTag);
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

    protected function createInBannerHtml(LibraryAdTagInterface $libraryAdTag)
    {
        $template = '<script src="%s" data-pv-tag-url=\'%s\' data-pv-platform="%s"%s</script>';
        $inBannerDescriptor = $libraryAdTag->getInBannerDescriptor();

        $vastTags = array_map(function(array $item) {
            return $item['tag'];
        }, $inBannerDescriptor['vastTags']);

        $vastTagStr = json_encode($vastTags, JSON_UNESCAPED_SLASHES);

        $html = sprintf($template, $this->inBannerVideoJsUrl, $vastTagStr, $inBannerDescriptor['platform'], "%s");
        if (is_numeric($inBannerDescriptor['timeout'])) {
            $html = sprintf($html, sprintf(" data-pv-timeout=\"%d\"", $inBannerDescriptor['timeout']). "%s");
        }

        if (is_numeric($inBannerDescriptor['playerWidth'])) {
            $html = sprintf($html, sprintf(" data-pv-width=\"%d\"", $inBannerDescriptor['playerWidth']). "%s");
        }

        if (is_numeric($inBannerDescriptor['playerHeight'])) {
            $html = sprintf($html, sprintf(" data-pv-height=\"%d\"", $inBannerDescriptor['playerHeight']));
        }

        return str_replace('%s', '', $html);
    }
}