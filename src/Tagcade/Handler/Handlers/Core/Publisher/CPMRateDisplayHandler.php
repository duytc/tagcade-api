<?php

namespace Tagcade\Handler\Handlers\Core\Publisher;

use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Handler\Handlers\Core\CPMRateDisplayHandlerAbstract;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\PlatformReportRepositoryInterface;
use DateTime;

class CPMRateDisplayHandler extends CPMRateDisplayHandlerAbstract
{
    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var PlatformReportRepositoryInterface
     */
    private $repository;

    function __construct(ObjectManager $om, PlatformReportRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }


    /**
     * @param UserRoleInterface $role
     * @return bool
     */
    public function supportsRole(UserRoleInterface $role)
    {
        return $role instanceof PublisherInterface;
    }

    /**
     * @inheritdoc
     */
    public function put(ModelInterface $entity, array $parameters)
    {
        $entity = parent::put($entity, $parameters);

        // Recalculate revenue for Yesterday data and then persist
        $startDate = $entity->getDate();
        $endDate = new DateTime(); // closest date after the reset
        $reports = $this->repository->getReportFor($startDate, $endDate);

        /**
         * @var ReportInterface $report
         */
        foreach($reports as $report) {
            // search for AgTag report then reset the estRevenue
            $report =
            $report->setCalculatedFields();
            $this->om->merge($report);
        }

    }

} 