<?php

namespace Tagcade\Service\CSV;


use Tagcade\Model\User\Role\PublisherInterface;

interface WhiteListImporterInterface
{
    /**
     * @param $filename
     * @param PublisherInterface $publisher
     * @param $name
     * @param null $csvSeparator
     * @return mixed
     */
    public function importCsv($filename, PublisherInterface $publisher, $name, $csvSeparator = null);
}