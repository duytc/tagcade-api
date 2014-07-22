<?php

namespace Tagcade\Bundle\ReportApiBundle\Tests\Controller;

use Tagcade\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;
use Tagcade\Tests\Fixtures\LoadUserData;
use Tagcade\Tests\Fixtures\LoadSiteData;
use Tagcade\Tests\Fixtures\Report\LoadSourceReportData;

class SourceReportControllerTest extends ApiTestCase
{
    public function setUp()
    {
        $this->loadFixtures([
            LoadUserData::class,
            LoadSiteData::class
        ]);

        $this->loadFixtures([
            LoadSourceReportData::class
        ], 'reports');
    }

    public function testAdminCanAccess()
    {
        $client = $this->getClientForUser('admin');
        $response = $this->makeGetSourceReportsActionRequest($client);

        $this->assertJsonResponse($response, 200);
    }

    public function testPubCanAccess()
    {
        $client = $this->getClientForUser('pub');
        $response = $this->makeGetSourceReportsActionRequest($client);

        $this->assertJsonResponse($response, 200);
    }

    public function testAnonCannotAccess()
    {
        $client = $this->getClientForUser(null);
        $response = $this->makeGetSourceReportsActionRequest($client);

        $this->assertJsonResponse($response, 401);
    }

    public function testPubCannotAccessNonOwnedSite()
    {
        $client = $this->getClientForUser('pub');
        $response = $this->makeGetSourceReportsActionRequest($client, [
            'siteId' => 3,
        ]);

        $this->assertEquals($response->getStatusCode(), 403);
    }

    public function testPubCannotAccessOwnedSite()
    {
        $client = $this->getClientForUser('pub');
        $response = $this->makeGetSourceReportsActionRequest($client, [
            'siteId' => 2,
        ]);

        $this->assertEquals($response->getStatusCode(), 200);
    }

    public function testCorrectNumberOfReportsForAdmin()
    {
        $reports = $this->getReportData([
            'to' => '140607',
            'rowLimit' => 2,
        ]);

        $this->assertEquals(count($reports), 2);
    }

    protected function getReportData(array $params)
    {
        $client = $this->getClientForUser('admin');
        $response = $this->makeGetSourceReportsActionRequest($client, $params);

        return json_decode($response->getContent());
    }

    /**
     * @param $client
     * @param array $params additional params to pass to the route
     * @return Response
     */
    protected function makeGetSourceReportsActionRequest(Client $client, array $params = [])
    {
        $params = array_merge([
            'siteId' => 1,
            'from'   => '140601',
        ], $params);

        $client->request('GET', $this->getUrl('report_api_1_get_sourcereports', $params));

        return $client->getResponse();
    }

}