<?php

namespace Tagcade\DomainManager;


use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Entity\Core\ImportedFile;
use Tagcade\Repository\Core\ImportedFileRepositoryInterface;

class ImportedFileManager implements ImportedFileManagerInterface
{
    /** @var ObjectManager $om */
    protected $om;
    /** @var ImportedFileRepositoryInterface $repository */
    protected $repository;

    function __construct(ObjectManager $om, ImportedFileRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function save(ImportedFile $importedFile)
    {
        if(!$importedFile instanceof ImportedFile) throw new \InvalidArgumentException('expect ImportedFile object');
        $this->om->persist($importedFile);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function findByHash($hash)
    {
        return $this->repository->findByHash($hash);
    }
}