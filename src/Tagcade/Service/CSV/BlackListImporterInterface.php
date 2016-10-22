<?php

namespace Tagcade\Service\CSV;


use Tagcade\Model\User\Role\PublisherInterface;

interface BlackListImporterInterface
{
    /**
     * @param $filename
     * @param PublisherInterface $publisher
     * @param $name
     * @param null $headerPosition
     * @param null $csvSeparator
     * @return mixed
     */
    public function importCsv($filename, PublisherInterface $publisher, $name, $headerPosition = null, $csvSeparator = null);
}