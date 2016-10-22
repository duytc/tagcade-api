<?php

namespace Tagcade\Service\CSV;


use Tagcade\Model\User\Role\PublisherInterface;

interface ListImporterInterface
{
    public function importCsv($filename, PublisherInterface $publisher, $name, $headerPosition = null, $csvSeparator = null);
}