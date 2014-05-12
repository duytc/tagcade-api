<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/getToken")
     */
    public function getTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }

    /**
     * @Route("/switchUser")
     */
    public function switchUserAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }

    /**
     * @Route("/test")
     */
    public function testAction()
    {
        return new JsonResponse("test");
    }
}
