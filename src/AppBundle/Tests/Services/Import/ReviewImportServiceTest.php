<?php
namespace AppBundle\Services\Import;

use AppBundle\Entity\Review;
use AppBundle\Entity\Hotel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/22/16
 * Time: 9:11 PM
 */
class ReviewImportServiceTest extends KernelTestCase
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

    /**
     * @param $row
     *
     * @dataProvider provideRow
     */
    public function testImport($row, $expectedResult)
    {
        $importService =  new ReviewImportService($this->registry);
        $importService->setData($row);
        $importService->import();
        $repository = $this->registry->getRepository(Review::class);
        $actualResult = $repository->findOneBy(array('review' => $row[ReviewImportService::INDEX_REVIEW]));
        
        $this->assertEquals($expectedResult->toArray(), $actualResult->toArray());

        $this->entityManager->remove($actualResult);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function provideRow()
    {
        return [
            'dataset normal case' => [
                'row' => [
                    ReviewImportService::INDEX_HOTEL => 'Hilton',
                    ReviewImportService::INDEX_REVIEW => 'Bla bala bala1',
                    ReviewImportService::INDEX_AUTHOR => 'Vesela',
                    ReviewImportService::INDEX_TOTAL_SCORE => 0,
                    ReviewImportService::INDEX_SCORE_DESCRIPTION => '',
                    
                ],
                'expected' => $this->makeReview('Hilton', 'Bla bala bala1','Vesela', 0, '')
            ],
            'dataset normal case 2' => [
                'row' => [
                    ReviewImportService::INDEX_HOTEL => 'Hilton',
                    ReviewImportService::INDEX_REVIEW => 'Bla bala bala2',
                    ReviewImportService::INDEX_AUTHOR => 'Vesela',
                    ReviewImportService::INDEX_TOTAL_SCORE => 1,
                    ReviewImportService::INDEX_SCORE_DESCRIPTION => 'bla +1',

                ],
                'expected' => $this->makeReview('Hilton', 'Bla bala bala2','Vesela', 1, 'bla +1')
            ],
            'dataset missing totalScore and description' => [
                'row' => [
                    ReviewImportService::INDEX_HOTEL => 'Hilton',
                    ReviewImportService::INDEX_REVIEW => 'Bla bala bala3',
                    ReviewImportService::INDEX_AUTHOR => 'Vesela',
                ],
                'expected' => $this->makeReview('Hilton', 'Bla bala bala3','Vesela', 0, '')
            ],
            'dataset missing description' => [
                'row' => [
                    ReviewImportService::INDEX_HOTEL => 'Hilton',
                    ReviewImportService::INDEX_REVIEW => 'Bla bala bala4',
                    ReviewImportService::INDEX_AUTHOR => 'Vesela',
                    ReviewImportService::INDEX_TOTAL_SCORE => 1,
                ],
                'expected' => $this->makeReview('Hilton', 'Bla bala bala4','Vesela', 1, '')
            ],

        ];
    }
    
    /**
     * @expectedExceptionMessage 'File is ith more columns that are allowed'
     */
    public function testImportThrowException()
    {
        $row = [
            ReviewImportService::INDEX_HOTEL => 'Hilton',
            ReviewImportService::INDEX_REVIEW => 'Bla bala bala5',
            ReviewImportService::INDEX_AUTHOR => 'Vesela',
            ReviewImportService::INDEX_TOTAL_SCORE => 1,
            ReviewImportService::INDEX_SCORE_DESCRIPTION => 'bala +1',
            ReviewImportService::INDEX_SCORE_DESCRIPTION => 'bala +1',
            ReviewImportService::INDEX_SCORE_DESCRIPTION => 'bala +1',
        ];
        $importService =  new ReviewImportService($this->registry);
        $importService->setData($row);
        $importService->import();
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @param $hotelName
     * @param $reviewDescription
     * @param $author
     * @param $totalScore
     * @param $scoreDescription
     * @return Review
     */
    private function makeReview($hotelName, $reviewDescription, $author, $totalScore, $scoreDescription)
    {
        $hotel = new Hotel();
        $hotel->setName($hotelName);
        
        $review = new Review();
        $review->setHotel($hotel);
        $review->setReview($reviewDescription);
        $review->setAuthor($author);
        $review->setTotalScore($totalScore);
        $review->setScoreDescription($scoreDescription);
        return $review;
    }
}