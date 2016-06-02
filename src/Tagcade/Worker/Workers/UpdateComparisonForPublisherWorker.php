<?php


namespace Tagcade\Worker\Workers;


use DateTime;
use stdClass;
use Symfony\Component\Validator\Constraints\Date;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\UnifiedReport\ReportComparisonCreatorInterface;

class UpdateComparisonForPublisherWorker {


    /**
     * @var ReportComparisonCreatorInterface
     */
    protected $comparisonCreator;
    /**
     * @var PublisherManagerInterface
     */
    private $publisherManager;

    /**
     * @param ReportComparisonCreatorInterface $comparisonCreator
     * @param PublisherManagerInterface $publisherManager
     */
    function __construct(ReportComparisonCreatorInterface $comparisonCreator, PublisherManagerInterface $publisherManager )
    {
        $this->comparisonCreator = $comparisonCreator;
        $this->publisherManager = $publisherManager;
    }

    /**
     * @param stdClass $params
     * @throws \Exception
     */

    public function UpdateComparisonForPublisher (StdClass $params)
    {
        $publisherId = $params->publisherId;
        $startDate =  new DateTime($params->startDate);
        $endDate   =  new DateTime($params->endDate);
        $override = $params->override;

        $publisher = $this->publisherManager->findPublisher($publisherId);

        if(!$publisher instanceof PublisherInterface) {
            throw new \Exception(sprintf('Can not find publisher which Id =%d', $publisherId));
        }

        $this->comparisonCreator->updateComparisonForPublisher($publisher, $startDate, $endDate, $override);

    }

}