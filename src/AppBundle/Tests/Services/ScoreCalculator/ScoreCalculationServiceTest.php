<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/7/16
 * Time: 7:16 PM
 */

namespace AppBundle\Services\ScoreCalculator;


use AppBundle\Entity\Criteria;
use AppBundle\Entity\Negative;
use AppBundle\Entity\Positive;
use AppBundle\Entity\Review;
use AppBundle\Entity\Hotel;

class ScoreCalculationServiceTest extends \PHPUnit_Framework_TestCase
{
    static private $positive = [
        'really good',
        'great',
        'good',
        'very nice',
        'amazing',
        'careful',
        'well',
        'helpful',
        'friendly',
        'clean',
        'cleanliness',
        'easy',
        'excellent',
        'top',
        'superb',
        'fantastic',
        'best',
        'comfortable',
        'perfect',
        'love',
        'going to come back',
        'made our stay',
        'was fun',
        'not far from',
        'very new',
        'super'
    ];

    static private $negative = [
        'problem',
        'unfriendly',
        'horrible',
        'stue',
        'unsupported',
        'average',
        'dirty',
        'negative',
        'unsupported',
        'hell',
        'bad',
        'didn\'t work',
        'ancient',
        'cold',
        'tiny',
        'small',
        'hard',
        'uncomfortable',
        'torn',
        'Stay away',
        'old',
        'decrepit',
        'terrible',
        'broken',
        'junk',
        'awful',
        'worst',
        'disgusting',
        'falling out',
        'minty',
        'thin',
        'nightmare',
        'freezing',
        'didn\'t sleep',
        'rude',
        'undisciplined',
        'fell off',
        'rotten',
        'mess',
        'surly',
        'never',
        'not going to come back',
        'very old',
    ];

    static $criteria = array(
        0 => [
            'name' => 'cost',
            'alternative_name' => 'price,bill',
        ],
        1 => [
            'name' => 'stuff',
            'alternative_name' => 'service,personnel,crew,he,she',
        ],
        2 => [
            'name' => 'bed',
            'alternative_name' => 'bed,bedroom,sleep quality,mattresses,linens'
        ],
        3 => [
            'name' => 'room',
            'alternative_name' => 'room,apartment,chamber'
        ],
        4 => [
            'name' => 'location',
            'alternative_name' => 'location,spot'
        ],
        5 => [
            'name' => 'hotel',
            'alternative_name' => 'hotel,property,lodge,resort'
        ],
        6 => [
            'name' => 'breakfast',
            'alternative_name' => 'breakfast'
        ],
        7 => [
            'name' => 'food',
            'alternative_name' => 'food,dinner,lunch'
        ],
        8 => [
            'name' => 'bathroom',
            'alternative_name' => 'bathroom,lavatory,shower,toilet,bath'
        ],
        9 => [
            'name' => 'restaurant',
            'alternative_name' => 'restaurant'
        ],
        10 => [
            'name' => 'pool',
            'alternative_name' => 'pool,spa,wellness'
        ],
        11 => [
            'name' => 'bar',
            'alternative_name' => 'bar,club'
        ],
    );

    public function setUp()
    {
        ExpressionBuilder::rebuildExpression(
            null,
            $this->makeCriteriaCollection(),
            $this->makePositiveCollection(),
            $this->makeNegativeCollection());
    }

    /**
     * @dataProvider providerPositive
     * @param $hotel
     * @param $reviewMessage
     * @param $criteria
     * @param $expectedResult
     * @param $expectedScore
     */
    public function testPositive($hotel, $reviewMessage, $author, $expectedResult, $expectedScore)
    {
        $newHotel = new Hotel();
        $newHotel->setName($hotel);

        $testReview = new Review();
        $testReview->setAuthor($author);
        $testReview->setHotel($newHotel);
        $testReview->setReview($reviewMessage);
        $scoreCalculationService = new ScoreCalculationService($testReview);

        $scoreCalculationService->run();

        $this->assertEquals($expectedResult, $scoreCalculationService->getScoreDescription());
        $this->assertEquals($expectedScore, $scoreCalculationService->getTotalScore());

    }

    /**
     * @return array
     */
    public function providerPositive()
    {
        return array(
            'dataset positive 1' => array(
                'hotel' => 'Hilton',
                'reviewMessage' => 'Found this hotel by reading over tripadvisor while planning a little beach getaway.
                 Really good price by the beach.
                 James the front desk manager was really fun, he made our stay more fun than we thought it would be.
                 We are going to come back with our friends soon.',
                'author' => 'Vesela',
                'expectedResults' => "really good price +1,made our stay +1,going to come back +1",
                'expectedScore' => 3
            ),
            'dataset positeve 2' => array(
                'hotel' => 'Royal',
                'reviewMessage' => 'Across the road from Santa Monica Pier is exactly where you want to be when visiting Santa Monica, as well as not far from lots of shops and restaurants/bars.
                                     Hotel itself is very new & modern, rooms were great. Comfortable beds & possibly the best shower ever!',
                'author' => 'Vesela',
                'expectedResults' => "hotel itself is very new +1,rooms were great +1,comfortable beds +1,best shower +1,not far from +1",
                'expectedScore' => 5
            ),
            'dataset positive 3' => array(
                'hotel' => 'Hilton',
                'reviewMessage' => 'I have stayed here 4 or 5 times while visiting LA.
                     Despite travelling all over the world and staying in some of the best international hotels ( for work),
                     Hotel Caliornia is one of my absolute favourites.
                     Perfect location, right on the beach. I love the way you can just open your door and be outside,
                     no elevators, corridors big glass windows.
                     The ambience is so nice, retro perfect. As for the staff, I have had consistently superb service,
                     with much more personal service and attention to detail than is usual in bigger hotels.
                     Aaron and Katy were just two who have been exemplary this time but really everyone is terrific.
                     Can\'t recommend it highly enough.',
                'author' => 'Visitor',
                'expectedResults' => 'perfect location +1,had consistently superb +1',
                'expectedScore' => 2,
            ),
            'dataset negative' => array(
                'hotel' => 'Hilton',
                'reviewMessage' => 'Terrible. Old, not quite clean. Lost my reservation, then "found" a smaller room,
                     for the same price, of course. Noisy. Absolutely no parking,
                      unless you luck out for the $10 spaces (of which there are 12).
                       Water in bathroom sink would not turn off. Not hair dryer, no iron in room.
                        Miniscule shower- better be thin to use it!',
                'author' => 'Visitor',
                'expectedResults' => 'smaller room -1,shower better be thin -1,not quite clean -1',
                'expectedScore' => -3,
            ),
            'dataset negative 2' => array(
                'hotel' => 'Hilton',
                'reviewMessage' => 'I was excited to stay at this Hotel. It looked cute and was reasonable. 
                    It turned out to be terrible. We were woken up both mornings at 5:45 AM by noisy neighbors.
                     The shower was clogged up...the room was sooooo small we kept tripping over each other.
                      The saving grace was the pool at the Loews next door.
                       I wish we had paid an extra $50 and stayed at a nicer place.
                        This motel should cost no more than $99 a night.',
                'author' => 'Visitor',
                'expectedResults' => 'room was sooooo small -1',
                'expectedScore' => -1,
            ),
            
        );
    }
    
    /**
     * @return array
     */
    private function makeNegativeCollection()
    {
        $negativeCollection = [];
        foreach (static::$negative as $negative) {
            $n = new Negative();
            $n->setNegative($negative);
            $negativeCollection[] = $n;
        }
        return $negativeCollection;
    }

    /**
     * @return array
     */
    private function makePositiveCollection()
    {
        $positiveCollection = [];
        foreach (static::$positive as $positive) {
            $p = new Positive();
            $p->setPositive($positive);
            $positiveCollection[] = $p;
        }
        return $positiveCollection;
    }

    /**
     * @return array
     */
    private function makeCriteriaCollection()
    {
        $criteriaCollection = [];
        foreach (static::$criteria as $c) {
            $testCriteria = new Criteria();
            $testCriteria->setName($c['name']);
            $testCriteria->setAlternativeName($c['alternative_name']);
            $criteriaCollection[] = $testCriteria;
        }
        return $criteriaCollection;
    }
}
