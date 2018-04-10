<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/20/16
 * Time: 8:57 PM
 */

namespace AppBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class CriteriaControllerTest extends WebTestCase
{
    
    public function setUp()
    {
        $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadCriteriaData'
        ));
    }
    /**
     * @dataProvider getHeader
     */
    public function testIndex($header)
    {

        $client = static::createClient();

        $crawler = $client->request('GET', '/criteria');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertContains(
            $header,
            $client->getResponse()->getContent());

        $link = $crawler->selectLink('Back')->link();

        $crawler = $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function getHeader()
    {
        return [
            'dataset' => [
                'header' => '<div class="container">
    <div class="jumbotron">
                                                <div class="btn-group btn-group-justified">
                <a href="/criteria" class="btn btn-primary">See Criteria</a>
                <a href="/positive" class="btn btn-primary">See Positive</a>
                <a href="/negative" class="btn btn-primary">See Negative</a>
                <a href="/hotel" class="btn btn-primary">See Hotels</a>
                <a href="/reviews/add" class="btn btn-primary">Import Reviews</a>
            </div>
            <br/>
        
    <div class="container">
        <a class="btn btn-success" href="/criteria/add">Create Criteria</a>
        <a class="btn btn-info back_home" href="/">Back</a>
'
            ]
        ];
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        return array(
            array('/'),
            array('/criteria'),
            array('/positive'),
            array('/negative'),
            array('/hotel'),
            array('/reviews/add'),
            array('/criteria/add'),
        );
    }

    public function testCreateFormSubmission()
    {
        $client = static::createClient();
        $client->insulate();

        $client->request('GET', '/criteria/add');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data['criteria_add_form[name]'] = 'beach';
        $data['criteria_add_form[alternative_name]'] = 'sea';

        $client->request(
            'POST',
            '/criteria/add',
            $data
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditFormSubmission()
    {
        $client = static::createClient();
        $client->insulate();

        $client->request('GET', '/criteria/edit/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $data['criteria_edit_form[name]'] = 'room';
        $data['criteria_edit_form[alternative_name]'] = 'room,apartment,penthouse';

        $client->request(
            'POST',
            '/criteria/edit/1',
            $data
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
