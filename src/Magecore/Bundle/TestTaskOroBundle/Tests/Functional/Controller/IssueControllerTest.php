<?php

namespace Magecore\Bundle\TestTaskOroBundle\Tests\Functional\Controller;

use Symfony\Component\DomCrawler\Form;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

/**
 * @outputBuffering enabled
 * @dbIsolation
 */
class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient(
            array(),
            array_merge($this->generateBasicAuthHeader(), array('HTTP_X-CSRF-Header' => 1))
        );
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('magecore_testtaskoro_issue'));
        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('magecore_testtaskoro.issue_create'));
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['magecore_testtaskoro_issue[summary]'] = 'Issue';
        $form['magecore_testtaskoro_issue[description]'] = 'disc';
        $form['magecore_testtaskoro_issue[priority]'] = 'low';
        $form['magecore_testtaskoro_issue[type]'] = 'Story';
        $form['magecore_testtaskoro_issue[reporter]'] = 1;

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        //Note, that data load from ajax, so only database request...
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'issues-grid',
            array('issue-grid[_filter][summary][value]' => 'Issue')
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $id = $result['id'];
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('magecore_testtaskoro.issue_update', array('id' => $result['id']))
        );
        /** @var Form $form */
        $form = $crawler->selectButton('Save and Close')->form();
        $form['magecore_testtaskoro_issue[summary]'] = 'Issue_update';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
       // $this->assertContains("Account saved", $crawler->html());

        return $id;
    }

    /**
     * @depends testUpdate
     */
    public function testView($id)
    {
        $crawler = $this->client->request(
            'GET',
            $this->getUrl('magecore_testtastoro.issue_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains("Issue_update - Issues - Activities", $crawler->html());
    }


    /**
     * @depends testUpdate
     */
    public function testDelete($id)
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('magecore_testtaskoro_api_delete_issue', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request(
            'GET',
            $this->getUrl('magecore_testtastoro.issue_view', array('id' => $id))
        );

        $result = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($result, 404);
    }
}
