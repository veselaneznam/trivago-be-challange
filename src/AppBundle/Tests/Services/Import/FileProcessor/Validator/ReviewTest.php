<?php

/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/23/16
 * Time: 5:44 PM
 */
namespace AppBundle\Services\Import\Validator;

use AppBundle\Entity\Hotel;
use AppBundle\Entity\Review as ReviewEntity;
class ReviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerValidator
     */
    public function testValidator($reviewData, $expectedMessage)
    {
       
        $review = new ReviewEntity();
        if(!empty($reviewData['hotel'])) {
            $hotel = new Hotel();
            $hotel->setName($reviewData['hotel']);
            $review->setHotel($hotel);
        }
        $review->setReview($reviewData['review']);
        $review->setAuthor($reviewData['author']);
        
        $validator = new Review($review);
        $validator->validate();
        $actualMessage = $validator->getMessage();
        $this->assertEquals($expectedMessage, $actualMessage);
    }
    
    public function providerValidator()
    {
        return [
            'dataset everything is there' => [
                'review' => [
                    'hotel' => 'Some Hotel',
                    'review' => 'Some review',
                    'author' => 'Some author'
                ],
                'message' => ''
            ],
            'dataset hotel missing' => [
                'review' => [
                    'hotel' => '',
                    'review' => 'Some review',
                    'author' => 'Some author'
                ],
                'message' => 'Hotel should be not empty'
            ],
            'dataset hotel, review missing' => [
                'review' => [
                    'hotel' => '',
                    'review' => '',
                    'author' => 'Some author'
                ],
                'message' => 'Hotel should be not empty;Review should be not empty'
            ],
            'dataset hotel, review, author missing' => [
                'review' => [
                    'hotel' => '',
                    'review' => '',
                    'author' => '',
                ],
                'message' => 'Hotel should be not empty;Author should be not empty;Review should be not empty'
            ],
        ];
    }
}