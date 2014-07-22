<?php

namespace Tagcade\Bundle\ApiBundle\Tests\Controller;

use Tagcade\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

use Tagcade\Tests\Fixtures\LoadUserData;
use Tagcade\Tests\Fixtures\LoadSiteData;

class SiteControllerTest extends ApiTestCase
{
    protected $executor;

    public function setUp()
    {
        $classes = array(
            LoadUserData::class,
            LoadSiteData::class
        );

        $this->fixtureExecutor = $this->loadFixtures($classes);
    }

    public function testPublisherCanCreateASiteWithUrlEncodedData()
    {
        $client = $this->getClientForUser('pub');

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(
                'name' => 'mysite.com',
                'domain' => 'mysite.com',
            ),
            array(),
            array('CONTENT_TYPE' => 'application/x-www-form-urlencoded')
        );

        $this->assertJsonResponse($client->getResponse(), 201, false);
    }

    public function testPublisherCanCreateASite()
    {
        $client = $this->getClientForUser('pub');

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"mysite.com","domain":"mysite.com"}'
        );

        $this->assertJsonResponse($client->getResponse(), 201, false);
    }

    public function testPublisherCannotCreateASiteWithInvalidData()
    {
        $client = $this->getClientForUser('pub');

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"my","domain":"2"}'
        );

        $this->assertJsonResponse($client->getResponse(), 400, false);
    }

    public function testPublisherCannotCreateASiteWithPublisher()
    {
        $client = $this->getClientForUser('pub');

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"mysite.com","domain":"mysite.com","publisher":"10"}'
        );

        $this->assertJsonResponse($client->getResponse(), 400, false);
    }

    public function testAdminCannotCreateASiteWithoutPublisher()
    {
        $client = $this->getClientForUser('admin');

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"name":"mysite.com","domain":"mysite.com"}'
        );

        $this->assertJsonResponse($client->getResponse(), 400, false);
    }

    public function testAdminCanCreateASiteWithPublisher()
    {
        $pub = $this->getFixtureReference('test-user-publisher1');

        $client = $this->getClientForUser('admin');

        $payload = array(
            'name' => 'mysite.com',
            'domain' => 'mysite.com',
            'publisher' => $pub->getId(),
        );

        $client->request(
            'POST',
            $this->getUrl('api_1_post_site'),
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode($payload)
        );

        $this->assertJsonResponse($client->getResponse(), 201, false);
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

        $this->assertJsonResponse($response, 200);
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