<?php

/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 2:56 PM
 */
namespace AppBundle\Services\ScoreCalculator;

use AppBundle\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Registry;

final class ScoreCalculationService
{
    const POSITIVE_DIR = 'positive';
    const NEGATIVE_DIR = 'negative';
    const REVERSE_PREFIX = '/(no*.).(\b\w+\b)?.';

    /**
     * @var Review
     */
    private $review;
    
    /**
     * @var Registry
     */
    private $registry;
    
    /**
     * @var string
     */
    private $scoreDescription = '';
    
    /**
     * @var int
     */
    private $totalScore = 0;

    /**
     * ScoreCalculationService constructor.
     * @param Review $review
     * @param $registry
     */
    public function __construct(Review $review, $registry = null)
    {
        $this->review = $review;

        $this->registry = $registry;
    }

    /**
     * @return array
     */
    public function run()
    {
        $this->getPositiveExpression();
        $this->getNegativeExpression();
        $this->analizeSentence($this->review->getReview());
    }

    /**
     * @param string $sentence
     * @return array
     */
    private function analizeSentence($sentence)
    {
        $sentence = $this->sanitizeSentence($sentence);
        if (isset(ExpressionBuilder::$positiveExpression)) {
            $positiveCollectionMatches = $this->generateMatches(
                $sentence,
                static::POSITIVE_DIR
            );
            $this->assignScore(static::POSITIVE_DIR, $positiveCollectionMatches);
            $sentence = str_replace($positiveCollectionMatches, '', $sentence);
        }

        if (!empty($sentence) && isset(ExpressionBuilder::$negativeExpression)) {
            $negativeCollectionMatches = $this->generateMatches(
                $sentence,
                static::NEGATIVE_DIR
            );

            $this->assignScore(static::NEGATIVE_DIR, $negativeCollectionMatches);
        }
    }

    /**
     * @return mixed
     */
    private function getPositiveExpression()
    {
        if (ExpressionBuilder::$positiveExpression === null) {
            if (isset($this->registry)) {
                ExpressionBuilder::rebuildExpression($this->registry);
            } else {
                ExpressionBuilder::rebuildExpression();
            }
        }
    }

    /**
     * @return mixed
     */
    private function getNegativeExpression()
    {
        if (ExpressionBuilder::$negativeExpression === null) {
            if (isset($this->registry)) {
                ExpressionBuilder::rebuildExpression($this->registry);
            } else {
                ExpressionBuilder::rebuildExpression();
            }
        }
    }

    /**
     * @param array $firstMatches
     * @param array $secondMatches
     * @return array
     */
    private function filterMatches(array $firstMatches, array $secondMatches)
    {

        $secondMatches = array_filter($secondMatches, function ($directionMatch) use ($firstMatches) {
            return (preg_grep('/' . $directionMatch . '/', $firstMatches)) ? false : true;
        });
        return array_merge($firstMatches, $secondMatches);
    }

    /**
     * @param string $sentence
     * @param string $direction
     * @return array
     */
    private function generateMatches($sentence, $direction)
    {
        $criteriaAndDirectionExpression = null;
        $directionExpression = null;
        $reverseExpression = null;
        $matches = array();

        switch ($direction) {
            case static::POSITIVE_DIR:
                $criteriaAndDirectionExpression = ExpressionBuilder::$positiveAndCriteriaExpression;
                $directionExpression = ExpressionBuilder::$positiveExpression;
                $reverseExpression = self::REVERSE_PREFIX . ExpressionBuilder::$negativeExpression . '/';
                break;
            case static::NEGATIVE_DIR:
                $criteriaAndDirectionExpression = ExpressionBuilder::$negativeAndCriteriaExpression;
                $directionExpression = ExpressionBuilder::$negativeExpression;
                $reverseExpression = self::REVERSE_PREFIX . ExpressionBuilder::$positiveExpression . '/';
        }

        if (isset($criteriaAndDirectionExpression)) {

            preg_match_all($criteriaAndDirectionExpression, $sentence, $criteriaMatches);

            preg_match_all($reverseExpression, $sentence, $reversedMatches);

            $matches = $this->filterMatches($criteriaMatches[0], $reversedMatches[0]);

            $matches = $this->removePunctuation($matches);
            
            $expression = $this->removeSingleWords($directionExpression);

            preg_match_all('/' . $expression . '/', $sentence, $directionMatches);
            $matches = $this->filterMatches($matches, $directionMatches[0]);
        }
        return $matches;
    }

    /**
     * @param $directionExpression
     * @return string
     */
    private function removeSingleWords($directionExpression)
    {
        $directionExpression = trim($directionExpression, '\)\(');

        $directionExpression = array_filter(explode('|', $directionExpression), function ($element) {
            return str_word_count($element) >= 2;
        });
        $directionExpression = '(' . implode('|', $directionExpression) . ')';
        return $directionExpression;
    }

    /**
     * @param $matches
     * @return mixed
     */
    private function removePunctuation($matches)
    {
        $matches = array_map(function ($match) {
            return preg_replace("#[[:punct:]]#", "", $match);
        }, $matches);
        return $matches;
    }

    /**
     * @return mixed
     */
    public function getScoreDescription()
    {
        return $this->scoreDescription;
    }

    /**
     * @return int
     */
    public function getTotalScore()
    {
        return $this->totalScore;
    }

    /**
     * @param $sentence
     * @return string
     */
    private function sanitizeSentence($sentence)
    {
        $sentence = str_replace(['&', '$'], '', $sentence);
        return strtolower($sentence);
    }

    /**
     * @param $direction
     * @param $matches
     */
    private function assignScore($direction, $matches)
    {
        foreach ($matches as $match) {
            switch ($direction) {
                case static::POSITIVE_DIR:
                    if (!empty($match)) {
                        $this->scoreDescription .= trim($match) . ' +1' . ",";
                        $this->totalScore++;
                    }
                    break;
                case static::NEGATIVE_DIR:
                    if (!empty($match)) {
                        $this->scoreDescription .= trim($match) . ' -1' . ",";
                        $this->totalScore--;
                    }
                    break;
            }
        }
        $this->scoreDescription = trim($this->scoreDescription, ",");
    }
}