<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/20/16
 * Time: 9:23 PM
 */

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ReviewController extends WebTestCase
{

    public function testCsvUpload()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/reviews/add');
        $this->assertStatusCode(200, $client);

        $form = $crawler->selectButton('Submit')->form();
        $csvFile = new UploadedFile(
        # Path to the file to send
            __DIR__ . '/data/trivagotest.csv',
            # Name of the sent file
            'trivagotest.csv',
            # MIME type
            'text/csv',
            # Size of the file
            9988
        );

        $form->setValues(['form[submitFile]' => $csvFile,]);
        $client->submit($form);

        $file = $form->get('form[submitFile]');

        $this->assertNotEmpty($file);
    }
}