<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(array(), $this->generateWsseAuthHeader());
    }

    /**
     * @return array
     */
    public function testCreate()
    {
        $request = array(
            "issue" => array (
                "summary" => 'Issue_name_' . mt_rand(),
                "description" => 'pum',
                "type" => 'Bug',
                "priority" => 'low',
                "reporter" => '1',
            )
        );

        $this->client->request(
            'POST',
            $this->getUrl('magecore_testtaskoro_api_post_issue'),
            $request
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 201);
        $this->assertArrayHasKey('id', $result);

        $request['id'] = $result['id'];
        return $request;
    }

    /**
     * @param array $request
     * @depends testCreate
     * @return array
     */
    public function testGet(array $request)
    {
        $this->client->request(
            'GET',
            $this->getUrl('magecore_testtaskoro_api_get_issue', array('id' => $request['id']))
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($request['issue']['code'], $result['code']);
    }

    /**
     * @param array $request
     * @depends testCreate
     * @depends testGet
     */
    public function testUpdate(array $request)
    {
        $request['issue']['summary'] .= "_Updated";
        $this->client->request(
            'PUT',
            $this->getUrl('magecore_testtaskoro_api_put_issue', array('id' => $request['id'])),
            $request
        );
        $result = $this->client->getResponse();

        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('magecore_testtaskoro_api_get_issue', array('id' => $request['id']))
        );

        $result = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals(
            $request['issue']['summary'],
            $result['summary']
        );
    }

    /**
     * @param array $request
     * @depends testCreate
     */
    public function testDelete(array $request)
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('magecore_testtaskoro_api_delete_issue', array('id' => $request['id']))
        );
        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);
        $this->client->request('GET', $this->getUrl(
            'magecore_testtaskoro_api_get_issue',
            array('id' => $request['id'])
        ));
        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
