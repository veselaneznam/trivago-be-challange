<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/8/16
 * Time: 8:53 PM
 */

namespace AppBundle\Services\ScoreCalculator;


use AppBundle\Entity\Criteria;
use AppBundle\Entity\Negative;
use AppBundle\Entity\Positive;
use Doctrine\Bundle\DoctrineBundle\Registry;

final class ExpressionBuilder
{
    /**
     * @var string
     */
    public static $positiveExpression;

    /**
     * @var string
     */
    public static $negativeExpression;

    /**
     * @var string
     */
    public static $positiveAndCriteriaExpression;

    /**
     * @var string
     */
    public static $negativeAndCriteriaExpression;
    
    /**
     * @var array
     */
    private $criteriaCollection;
    
    /**
     * @var array
     */
    private $positiveCollection;
    
    /**
     * @var array
     */
    private $negativeCollection;


    /**
     * @param array $criteriaCollection
     * @param array $positiveCollection
     * @param array $negativeCollection
     * @param Registry|null $registry
     */
    private function __construct(
        Registry $registry = null,
        array $criteriaCollection = array(),
        array $positiveCollection = array(),
        array $negativeCollection = array()
    ) {
        if (isset($registry)) {
            if(empty($criteriaCollection)) {
                $criteriaCollection = $registry->getRepository('AppBundle:Criteria')->findAll();
            }

            if(empty($positiveCollection)) {
                $positiveCollection = $registry->getRepository('AppBundle:Positive')->findAll();
            }

            if(empty($negativeCollection)) {
                $negativeCollection = $registry->getRepository('AppBundle:Negative')->findAll();
            }    
        }
                
        $this->criteriaCollection = $criteriaCollection;
        $this->positiveCollection = $positiveCollection;
        $this->negativeCollection = $negativeCollection;
    }
    
    private function __clone() {
        
    }

    /**
     * @param Registry|null $registry
     * @param array $criteriaCollection
     * @param array $positiveCollection
     * @param array $negativeCollection
     */
    public static function rebuildExpression(
        Registry $registry = null,
        array $criteriaCollection = array(),
        array $positiveCollection = array(),
        array $negativeCollection = array()
    ) {
        $expressionBuilder = new ExpressionBuilder(
            $registry,
            $criteriaCollection,
            $positiveCollection,
            $negativeCollection
        );
        $expressionBuilder->reset();
        $expressionBuilder->makePositiveExpression();
        $expressionBuilder->makeNegativeExpression();
        $expressionBuilder->buildExpression();
    }
    
    /**
     * @return \Generator
     */
    private function generatePositive()
    {
        foreach ($this->positiveCollection as $positive) {
            yield $positive;
        }
    }

    /**
     * @return \Generator
     */
    private function generateCriteria()
    {
        foreach ($this->criteriaCollection as $criteria) {
            yield $criteria;
        }
    }

    /**
     * @return \Generator
     */
    private function generateNegative()
    {
        foreach ($this->negativeCollection as $negative) {
            yield $negative;
        }
    }

    /**
     * @return string
     */
    private function makePositiveExpression()
    {
        /**
         * @var Positive $positive
         */
        if(self::$positiveExpression === null) {
            foreach ($this->generatePositive() as $positive) {
                self::$positiveExpression .= trim($positive->getPositive()) . '|';
            }

            self::$positiveExpression = trim(self::$positiveExpression, '|');

            if('' != self::$positiveExpression) {
                self::$positiveExpression = '(' . self::$positiveExpression  . ')';
            }
        }
    }

    /**
     * @return string
     */
    private function makeCriteriaExpression()
    {
        $criteriaExpression = '';

        /**
         * @var Criteria $criteria
         */
        foreach ($this->generateCriteria() as $criteria) {
            $criteriaExpression .=
                trim($criteria->getName())
                . '*.|'
                . implode('*.|', explode(',' , trim($criteria->getAlternativeName())))
                . '*.|';
        }

        $criteriaExpression = trim($criteriaExpression, '*.|');
        return ('' != $criteriaExpression) ? '(' . $criteriaExpression . ')' : '';
    }

    /**
     * @return string
     */
    private function makeNegativeExpression()
    {
        if(self::$negativeExpression === null) {
            /**
             * @var Negative $negative
             */
            foreach ($this->generateNegative() as $negative) {
                self::$negativeExpression .= trim($negative->getNegative()) . '|';
            }
            self::$negativeExpression = trim(self::$negativeExpression, '|');

            if('' != self::$negativeExpression) {
                self::$negativeExpression = '(' . self::$negativeExpression  . ')';
            }
        }        
    }

    private function buildExpression()
    {
        if (self::$positiveAndCriteriaExpression === null && self::$positiveExpression != '') {

            self::$positiveAndCriteriaExpression = '/'
                .'('
                . self::$positiveExpression
                . '([a-z]+)?([a-z]+)?'
                . '.'
                . $this->makeCriteriaExpression() 
                . '(\W|$)'
                . ')|('
                . '(\W|^)'
                . $this->makeCriteriaExpression()
                . '.([a-z]+)?.([a-z]+)?'
                . '.'
                . self::$positiveExpression
                . ')/';
        }

        if (self::$negativeAndCriteriaExpression === null && self::$negativeExpression != '') {
            self::$negativeAndCriteriaExpression = '/'
                .'('
                . self::$negativeExpression
                . '([a-z]+)?([a-z]+)?'
                . '.'
                . $this->makeCriteriaExpression() 
                . '(\W|$)'
                . ')|('
                . '(\W|^)'
                .$this->makeCriteriaExpression()
                . '.([a-z]+)?.([a-z]+)?'
                . '.'
                . self::$negativeExpression 
                . ')/';
        }
    }

    private function reset()
    {
        self::$positiveExpression = null;
        self::$negativeExpression = null;
        self::$positiveAndCriteriaExpression = null;
        self::$negativeAndCriteriaExpression = null;
    }
}