<?php

/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/23/16
 * Time: 3:41 PM
 */
namespace AppBundle\Services\Import\FileProcessing;

use AppBundle\Services\Import\ReviewImportService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CsvTest extends KernelTestCase
{

    private $registry;
    
    private $entityManager;

    protected function setUp()
    {
        self::bootKernel();

        $this->registry = static::$kernel->getContainer()->get('doctrine');
        $this->entityManager = $this->registry->getManager();
    }
    protected function tearDown()
    {
        parent::tearDown();
        $this->entityManager->close();
    }
    
    public function testProcessNoErrors()
    {
        $importService = new ReviewImportService($this->registry);
        $fileProcessing = new Csv($importService);
        list($counter, $warnings) = $fileProcessing->process(__DIR__ . '/data/trivagotest.csv');
        $this->assertEquals(5, $counter);
        $this->assertEmpty($warnings);
    }

    public function testProcessWithErrors()
    {
        $importService = new ReviewImportService($this->registry);
        $fileProcessing = new Csv($importService);
        $expectedWarnings = [
            'Warning for line 4 : Hotel should be not empty',
            'Warning for line 6 : Review should be not empty'
        ];
        list($counter, $warnings) = $fileProcessing->process(__DIR__ . '/data/trivagotest_with_errors.csv');
        $this->assertEquals(3, $counter);
        $this->assertEquals($expectedWarnings, $warnings);
    }
    
    /**
     * @expectedExceptionMessage 'File is empty'
     */
    public function testProcessWithEmptyFile()
    {
        $importService = new ReviewImportService($this->registry);
        $fileProcessing = new Csv($importService);
        $fileProcessing->process(__DIR__ . '/data/trivagotest_empty.csv');
    }
    
    public function testProcessWithEmptyLine()
    {
        $importService = new ReviewImportService($this->registry);
        $fileProcessing = new Csv($importService);
        $expectedWarnings = [
            'Warning for line 4 : Hotel should be not empty;Author should be not empty;Review should be not empty',
        ];
        
        list($counter, $warnings) = $fileProcessing->process(__DIR__ . '/data/trivagotest_empty_line.csv');
        
        $this->assertEquals(4, $counter);
        $this->assertEquals($expectedWarnings, $warnings);
    }

    public function testProcessWithEmptyMandatory()
    {
        $importService = new ReviewImportService($this->registry);
        $fileProcessing = new Csv($importService);
        $expectedWarnings = [
            'Warning for line 2 : Hotel should be not empty;Author should be not empty;Review should be not empty',
        ];
        
        list($counter, $warnings) = $fileProcessing->process(__DIR__ . '/data/trivagotest_empty_mandatory.csv');
        $this->assertEquals(4, $counter);
        $this->assertEquals($expectedWarnings, $warnings);        
    }
}