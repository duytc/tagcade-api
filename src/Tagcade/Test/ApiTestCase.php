<?php

namespace Tagcade\Test;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTestCase extends WebTestCase
{
    protected $fixtureExecutor;

    protected function getFixtureReference($ref)
    {
        if ($this->fixtureExecutor instanceof AbstractExecutor) {
            $repo = $this->fixtureExecutor->getReferenceRepository();

            if ($repo->hasReference($ref)) {
                return $repo->getReference($ref);
            }

            throw new \Exception('that fixture reference does not exist');
        }

        throw new \Exception('executor is not set, did you call loadFixtures?');
    }

    protected function getClient($accepts = 'application/json')
    {
        $client = static::createClient();

        if ($accepts) {
            $client->setServerParameter('HTTP_Accept', $accepts);
        }

        return $client;
    }

    protected function getClientForUser($user = null, $client = null)
    {
        if (!$client) {
            $client = $this->getClient();
        }

        if ($user) {
            $jwt = $this->getJWT($user);
            $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $jwt->getTokenString()));
        }

        return $client;
    }

    /**
     * @param $user
     * @return \Namshi\JOSE\JWS;
     */
    protected function getJWT($user)
    {
        return $this->getContainer()->get('lexik_jwt_authentication.jwt_encoder')->encode([
            'username' => $user,
        ]);
    }

    protected function assertJsonResponse(
        Response $response,
        $statusCode = 200,
        $checkValidJson = true,
        $contentType = 'application/json'
    )
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );

        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}