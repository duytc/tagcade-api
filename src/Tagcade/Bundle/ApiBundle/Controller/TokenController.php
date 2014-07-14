<?php

namespace Tagcade\Bundle\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\RequestParam;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;

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

    /**
     * Regenerate a token for the current authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshTokenAction(Request $request)
    {
        // todo : waiting for 3rd party bundle to be refactored so this can be moved to a service

        $user = $this->getUser();

        $jwt = $this->get('lexik_jwt_authentication.jwt_manager')
            ->create($user)
        ;

        $event = new AuthenticationSuccessEvent(array('token' => $jwt), $user, $request);
        $this->get('event_dispatcher')->dispatch(Events::AUTHENTICATION_SUCCESS, $event);

        return new JsonResponse($event->getData());
    }
}
