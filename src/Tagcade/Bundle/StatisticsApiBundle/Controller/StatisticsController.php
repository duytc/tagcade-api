<?php

namespace Tagcade\Bundle\StatisticsApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;

class StatisticsController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @Rest\Get("/statistics/platform")
     *
     * Get statistics for the platform with optional date range.
     *
     * @return array
     */
    public function getPlatformAction()
    {
        return $this->get('tagcade.service.statistics')
            ->getAdminDashboard();
    }

    /**
     *
     * @Rest\Get("/statistics/account/{publisherId}", requirements={"publisherId" = "\d+"})
     *
     * Get statistics for a publisher with optional date range.
     *
     * @param int $publisherId
     *
     * @return array
     */
    public function getAccountReportAction($publisherId)
    {
        $publisher = $this->get('tagcade_user.domain_manager.user')->findPublisher($publisherId);

        if (!$publisher) {
            throw new NotFoundHttpException('That publisher does not exist');
        }

        if ( false === $this->get('security.context')->isGranted('view', $publisher) ) {
            throw new AccessDeniedException('You do not have permission to view this');
        }

        return $this->get('tagcade.service.statistics')
            ->getPublisherDashboard($publisher);
    }
}
