<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/6/16
 * Time: 9:14 PM
 */

namespace AppBundle\Services\Import\Validator;


use AppBundle\Entity\Review as ReviewEntity;

class Review implements ValidatorInterface
{
    private $messages = array();
    
    /**
     * @var ReviewEntity
     */
    private $review;

    /**
     * Review constructor.
     * @param ReviewEntity $review
     */
    public function __construct(ReviewEntity $review)
    {
        $this->review = $review;
    }

    public function validate()
    {
        if(empty($this->review->getHotel())) {
            $this->messages[] = 'Hotel should be not empty';
        }
        
        if(empty($this->review->getAuthor())) {
            $this->messages[] = 'Author should be not empty';
        }
        
        if(empty($this->review->getReview())) {
            $this->messages[] = 'Review should be not empty';
        }
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return implode(';', $this->messages);
    }
}