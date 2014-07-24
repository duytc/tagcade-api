<?php

namespace Tagcade\Bundle\AdminApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Tagcade\Test\ApiTestCase;

use Tagcade\Tests\Fixtures\LoadUserData;

class UserControllerTest extends ApiTestCase
{
    protected $executor;

    public function setUp()
    {
        $classes = array(
            LoadUserData::class
        );

        $this->loadFixtures($classes);
    }

    public function testPublisherCannotCreateAUser()
    {
        $client = $this->getClientForUser('pub');
        $response = $this->createPostUserRequest($client);
        $this->assertJsonResponse($response, 403);
    }

    public function testAnonCannotCreateAUser()
    {
        $client = $this->getClient();
        $response = $this->createPostUserRequest($client);
        $this->assertJsonResponse($response, 401);
    }

    public function testAdminCanCreateAUser()
    {
        $client = $this->getClientForUser('admin');
        $response = $this->createPostUserRequest($client);
        $this->assertJsonResponse($response, 201, $checkValidJson = false);
    }

    public function testCannotCreateASiteWithMissingFields()
    {
        $client = $this->getClientForUser('admin');

        $payload = array(
            // missing username
            'email' => 'my@email.com',
            'plainPassword' => '12345'
        );

        $response = $this->doPostUserRequest($payload, $client);
        $this->assertJsonResponse($response, 400);
    }

    public function testRoleValuesAreTransformed()
    {
        $client = $this->getClientForUser('admin');

        $payload = array(
            'username' => 'myroletest',
            'plainPassword' => '12345',
            'role' => 'publisher',
            'features' => ['analytics', 'display']
        );

        $response = $this->doPostUserRequest($payload, $client);
        $this->assertJsonResponse($response, 201, $checkValidJson = false);

        $newResourceLocation = $response->headers->get('location');
        $this->assertNotNull($newResourceLocation);

        $response = null;

        $client->request('GET', $newResourceLocation);
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);

        $rawData = $response->getContent();
        $user = json_decode($rawData, true);
        $this->assertEquals(3, count($user['roles']));
    }

    /**
     * @param Client|null $client
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function createPostUserRequest(Client $client = null)
    {
        $payload = array(
            'username' => 'myuser',
            'plainPassword' => '12345',
        );

        return $this->doPostUserRequest($payload, $client);
    }

    public function doPostUserRequest(array $payload, Client $client)
    {
        return $this->makeJsonRequest($payload, 'admin_api_1_post_user', 'POST', $client);
    }
}