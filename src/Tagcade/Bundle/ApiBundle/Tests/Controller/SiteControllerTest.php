<?php

namespace Tagcade\Bundle\ApiBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class SiteControllerTest extends WebTestCase
{
    public function setUp()
    {
        $classes = array(
            'Tagcade\Bundle\ApiBundle\Tests\Fixtures\LoadUserData',
            'Tagcade\Bundle\ApiBundle\Tests\Fixtures\LoadSiteData',
        );
        $this->loadFixtures($classes);
    }

    public function testJsonGetSitesActionWithValidUser()
    {
        $client = $this->getClientForUser('pub');
        $response = $this->makeGetSitesActionRequest($client);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testXmlGetSitesActionWithValidUserAndAcceptHeader()
    {
        $client = $this->getClient('application/xml');
        $client = $this->getClientForUser('pub', $client);
        $response = $this->makeGetSitesActionRequest($client);

        libxml_use_internal_errors(true);
        $xmlLoaded = (bool) simplexml_load_string($response->getContent());

        $this->assertTrue($xmlLoaded);
    }

    public function testJsonGetSitesActionWithInvalidUser()
    {
        $client = static::createClient();
        $response = $this->makeGetSitesActionRequest($client);
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testJsonGetSitesActionWithInvalidRole()
    {
        $client = $this->getClientForUser('admin');
        $response = $this->makeGetSitesActionRequest($client);
        $this->assertEquals(403, $response->getStatusCode());
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

    /**
     * @param $client
     * @return Response
     */
    protected function makeGetSitesActionRequest(Client $client)
    {
        $client->request('GET', $this->getUrl('api_1_get_sites'));

        return $client->getResponse();
    }
}