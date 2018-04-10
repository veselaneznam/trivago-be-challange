<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/20/16
 * Time: 11:49 PM
 */

namespace AppBundle\Services\ScoreCalculator;


use AppBundle\Entity\Criteria;
use AppBundle\Entity\Negative;
use AppBundle\Entity\Positive;

class ExpressionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $criteria
     * @param $positive
     * @param $negative
     * @param $expectedResult
     *
     * @dataProvider giveExpression
     */
    public function testRebuildExpression($criteria, $positive, $negative, $expectedResult)
    {
        ExpressionBuilder::rebuildExpression(
            null,
            $this->makeCriteriaCollection($criteria),
            $this->makePositiveCollection($positive),
            $this->makeNegativeCollection($negative)
        );

        $this->assertEquals(
            $expectedResult['positive_and_criteria_expression'],
            ExpressionBuilder::$positiveAndCriteriaExpression
        );

        $this->assertEquals(
            $expectedResult['negative_and_criteria_expression'],
            ExpressionBuilder::$negativeAndCriteriaExpression
        );

        $this->assertEquals(
            $expectedResult['positive_expression'],
            ExpressionBuilder::$positiveExpression
        );

        $this->assertEquals(
            $expectedResult['negative_expression'],
            ExpressionBuilder::$negativeExpression
        );
    }

    public function giveExpression()
    {
        return [
            'dataset only positive' => [
                'criteria' => [
                    0 => [
                        'name' => 'hotel',
                        'alternative_name' => 'loge,hotel'
                    ]
                ],
                'positive' => ['good', 'nice'],
                'negative' => [],
                'expectedResult' => [
                    'positive_and_criteria_expression' => '/((good|nice)([a-z]+)?([a-z]+)?.(hotel*.|loge*.|hotel)(\W|$))|((\W|^)(hotel*.|loge*.|hotel).([a-z]+)?.([a-z]+)?.(good|nice))/',
                    'negative_and_criteria_expression' => '',
                    'positive_expression' => '(good|nice)',
                    'negative_expression' => '',
                ]
            ],
            'dataset only negative' => [
                'criteria' => [
                    0 => [
                        'name' => 'hotel',
                        'alternative_name' => 'loge,hotel'
                    ]
                ],
                'positive' => [],
                'negative' => ['bad', 'not come'],
                'expectedResult' => [
                    'positive_and_criteria_expression' => '',
                    'negative_and_criteria_expression' => '/((bad|not come)([a-z]+)?([a-z]+)?.(hotel*.|loge*.|hotel)(\W|$))|((\W|^)(hotel*.|loge*.|hotel).([a-z]+)?.([a-z]+)?.(bad|not come))/',
                    'positive_expression' => '',
                    'negative_expression' => '(bad|not come)',
                ]
            ],
            'dataset no positive and no negative' => [
                'criteria' => [
                    0 => [
                        'name' => 'hotel',
                        'alternative_name' => 'loge,hotel'
                    ]
                ],
                'positive' => [],
                'negative' => [],
                'expectedResult' => [
                    'positive_and_criteria_expression' => '',
                    'negative_and_criteria_expression' => '',
                    'positive_expression' => '',
                    'negative_expression' => '',
                ]
            ],
            'dataset positive and negative' => [
                'criteria' => [
                    0 => [
                        'name' => 'hotel',
                        'alternative_name' => 'loge,hotel'
                    ]
                ],
                'positive' => ['good', 'nice'],
                'negative' => ['bad', 'not come'],
                'expectedResult' => [
                    'positive_and_criteria_expression' => '/((good|nice)([a-z]+)?([a-z]+)?.(hotel*.|loge*.|hotel)(\W|$))|((\W|^)(hotel*.|loge*.|hotel).([a-z]+)?.([a-z]+)?.(good|nice))/',
                    'negative_and_criteria_expression' => '/((bad|not come)([a-z]+)?([a-z]+)?.(hotel*.|loge*.|hotel)(\W|$))|((\W|^)(hotel*.|loge*.|hotel).([a-z]+)?.([a-z]+)?.(bad|not come))/',
                    'positive_expression' => '(good|nice)',
                    'negative_expression' => '(bad|not come)',
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    private function makeNegativeCollection($negative)
    {
        $negativeCollection = [];
        foreach ($negative as $negative) {
            $n = new Negative();
            $n->setNegative($negative);
            $negativeCollection[] = $n;
        }
        return $negativeCollection;
    }

    /**
     * @return array
     */
    private function makePositiveCollection($positive)
    {
        $positiveCollection = [];
        foreach ($positive as $positive) {
            $p = new Positive();
            $p->setPositive($positive);
            $positiveCollection[] = $p;
        }
        return $positiveCollection;
    }

    /**
     * @return array
     */
    private function makeCriteriaCollection($criteria)
    {
        $criteriaCollection = [];
        foreach ($criteria as $c) {
            $testCriteria = new Criteria();
            $testCriteria->setName($c['name']);
            $testCriteria->setAlternativeName($c['alternative_name']);
            $criteriaCollection[] = $testCriteria;
        }
        return $criteriaCollection;
    }
}