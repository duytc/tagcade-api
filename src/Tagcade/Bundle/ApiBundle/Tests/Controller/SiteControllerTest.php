<?php

namespace Tagcade\Bundle\ApiBundle\Tests\Controller;

use Tagcade\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

class SiteControllerTest extends ApiTestCase
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

        $this->assertJsonResponse($response, 200);
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
        $client = $this->getClient();
        $response = $this->makeGetSitesActionRequest($client);

        $this->assertJsonResponse($response, 401);
    }

    public function testJsonGetSitesActionWithInvalidRole()
    {
        $client = $this->getClientForUser('admin');
        $response = $this->makeGetSitesActionRequest($client);

        $this->assertJsonResponse($response, 403);
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