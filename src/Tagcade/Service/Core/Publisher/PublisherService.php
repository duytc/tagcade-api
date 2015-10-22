<?php


namespace Tagcade\Service\Core\Publisher;


use Doctrine\ORM\EntityManagerInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class PublisherService implements PublisherServiceInterface
{


    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;

    function __construct(EntityManagerInterface $em, PublisherManagerInterface $publisherManager)
    {
        $this->em = $em;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @return int
     */
    public function GenerateAndAssignUuid()
    {
        $count = 0;
        $allPublisher = $this->publisherManager->allPublishers();
        /**
         * @var PublisherInterface $publisher
         */
        foreach($allPublisher as $publisher) {
            if ($publisher->getUuid() === null) {
                $count++;
                $publisher->generateAndAssignUuid();
                $this->em->merge($publisher);
            }
        }

        $this->em->flush();

        return $count;
    }
}