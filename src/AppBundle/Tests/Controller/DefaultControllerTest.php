<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    /**
     * @dataProvider getHeader
     */
    public function testIndex($header)
    {
        $client = static::createClient();
        $client->insulate();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains($header
            , $client->getResponse()->getContent());

        $link = $crawler->selectLink('RUN')->link();

        $crawler = $client->click($link);

        $this->assertTrue(
            $client->getResponse()->isRedirect('/')
        );
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
        <a href="/calculate-score" class="btn btn-success">RUN</a>
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
        );
    }
}
