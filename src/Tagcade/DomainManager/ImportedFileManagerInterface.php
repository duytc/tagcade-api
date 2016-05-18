<?php

namespace Tagcade\DomainManager;


use Tagcade\Entity\Core\ImportedFile;

interface ImportedFileManagerInterface
{
    /**
     * persist an importedFile to database
     * @param ImportedFile $importedFile
     * @return mixed
     */
    public function save(ImportedFile $importedFile);

    /**
     * find an importedFile by hash
     * @param $hash
     * @return null|array|ImportedFile[]
     */
    public function findByHash($hash);
}