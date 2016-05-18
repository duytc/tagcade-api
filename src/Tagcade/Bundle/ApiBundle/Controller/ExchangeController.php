<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Tagcade\Model\Core\AdTagInterface;

/**
 * @Rest\RouteResource("Exchange")
 */
class ExchangeController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Security("has_role('ROLE_ADMIN')")
     *
     * Get all exchange
     * @ApiDoc(
     *  section="Ad Tags",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @return AdTagInterface[]
     */
    public function cgetAction()
    {
        return $this->container->getParameter('rtb.exchanges');
    }
}
