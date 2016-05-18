<?php


namespace Tagcade\Service\Core\AdTag;


use Tagcade\Model\Core\LibraryAdTagInterface;

interface PartnerTagIdMatcherInterface
{
    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @return mixed
     */
    public function extractPartnerTagId(LibraryAdTagInterface $libraryAdTag);
}