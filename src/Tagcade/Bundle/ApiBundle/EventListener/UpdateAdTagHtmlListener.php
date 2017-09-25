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
        /*
         * <script
         *   src="/inbannervideo.js"
         *   data-pv-tag-url='[
         *       "http://vast-tag-server.tagcade.dev:9998/tag.php?id=73c441f2-87a4-4d41-9352-e53127d3bd20"
         *   ]'
         *   data-pv-timeout="10"
         *   data-pv-width="300"
         *   data-pv-height="250"
         *   data-pv-slot="1"
         *   data-pv-tag="2"></script>
         * where:
         * - required: src, data-pv-tag-url, data-pv-width, data-pv-height
         * - auto: data-pv-slot, data-pv-tag
         */
        $template = ''
            . '<script'
            . ' src="%s"'
            . ' data-pv-tag-url=\'%s\''
            . ' $$DATA-PV-TIMEOUT$$'  // null for auto use timeout
            . ' $$DATA-PV-WIDTH$$'  // null for auto use slot width
            . ' $$DATA-PV-HEIGHT$$' // null for auto use slot height
            . ' $$DATA-PV-SLOT$$' // auto on cache
            . ' $$DATA-PV-TAG$$' // auto on cache
            . '></script>';

        $inBannerDescriptor = $libraryAdTag->getInBannerDescriptor();

        $vastTags = array_map(function (array $item) {
            return $item['tag'];
        }, $inBannerDescriptor['vastTags']);

        $vastTagStr = json_encode($vastTags, JSON_UNESCAPED_SLASHES);

        $html = sprintf($template, $this->inBannerVideoJsUrl, $vastTagStr);

        if (is_numeric($inBannerDescriptor['timeout'])) {
            $html = str_replace('$$DATA-PV-TIMEOUT$$', sprintf('data-pv-timeout="%d"', $inBannerDescriptor['timeout']), $html);
        } else {
            $html = str_replace('$$DATA-PV-TIMEOUT$$', '', $html);
        }

        if (is_numeric($inBannerDescriptor['playerWidth'])) {
            $html = str_replace('$$DATA-PV-WIDTH$$', sprintf('data-pv-width="%d"', $inBannerDescriptor['playerWidth']), $html);
        }

        if (is_numeric($inBannerDescriptor['playerHeight'])) {
            $html = str_replace('$$DATA-PV-HEIGHT$$', sprintf('data-pv-height="%d"', $inBannerDescriptor['playerHeight']), $html);
        }

        return $html;
    }
}