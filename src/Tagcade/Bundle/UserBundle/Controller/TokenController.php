<?php

namespace Tagcade\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RequestParam;

class TokenController extends Controller
{
    /**
     * Retrieve a JSON Web Token
     *
     * @RequestParam(name="username")
     * @RequestParam(name="password")
     *
     * @ApiDoc(
     * statusCodes={
     *  200 = "Successful login",
     *  401 = "Login failed"
     * })
     */
    public function getTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }
}
