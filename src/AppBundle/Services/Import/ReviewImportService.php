<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/4/16
 * Time: 3:05 PM
 */

namespace AppBundle\Services\Import;

use AppBundle\Entity\Hotel;
use AppBundle\Entity\HotelRepository;
use AppBundle\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Services\Import\Validator\Review as ReviewValidator;

class ReviewImportService implements ImportInterface
{
    const INDEX_HOTEL = 0;
    const INDEX_REVIEW = 1;
    const INDEX_AUTHOR = 2;
    const INDEX_TOTAL_SCORE = 3;
    const INDEX_SCORE_DESCRIPTION = 4;
    const MAX_COL_COUNT = 5;

    /**
     * @var array
     */
    private $data;
    /**
     * @var ObjectManager
     */
    private $registry;

    /**
     * @var string|null
     */
    private $warning;

    public function __construct(
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->warning = null;
    }

    /**
     * @throws \Exception
     */
    public function import()
    {
        if(count($this->data) > static::MAX_COL_COUNT) {
            throw new \Exception('File is with more columns that are allowed');
        }
        $review = new Review();
        $review->setReview($this->data[static::INDEX_REVIEW]);
        $review->setAuthor($this->data[static::INDEX_AUTHOR]);
        $review->setHotel($this->getHotel());

        $validator = new ReviewValidator($review);
        $validator->validate();

        if (empty($validator->getMessage())) {
            $totalScore = isset($this->data[static::INDEX_TOTAL_SCORE])
                ? $this->data[static::INDEX_TOTAL_SCORE]
                : '';

            $scoreDescription= isset($this->data[static::INDEX_SCORE_DESCRIPTION])
                ? $this->data[static::INDEX_SCORE_DESCRIPTION]
                : '';
            
            $review->setScoreDescription($scoreDescription);            
            $review->setTotalScore($totalScore);
            $this->registry->getManager()->persist($review);
            $this->registry->getManager()->flush();
        } else {
            $this->warning = $validator->getMessage();
        }
    }

    /**
     * @return string|null
     */
    public function getWarning()
    {
        return $this->warning;
    }

    public function reset()
    {
        $this->data = null;
        $this->warning = null;
    }

    public function setData(array $row)
    {
        $this->data = array_pad ($row , static::MAX_COL_COUNT, '' );
    }

    /**
     * @return mixed
     */
    private function getHotel()
    {
        /**
         * @var HotelRepository $hotelRepository
         */
        $hotelRepository = $this->registry->getRepository('AppBundle:Hotel');
        $hotel = $hotelRepository->getHotelByName($this->data[static::INDEX_HOTEL]);
        if (!$hotel instanceof Hotel && !empty($this->data[static::INDEX_HOTEL])) {
            $hotel = new Hotel();
            $hotel->setName($this->data[static::INDEX_HOTEL]);
            $this->registry->getManager()->persist($hotel);
            $this->registry->getManager()->flush();
        }
        return $hotel;
    }
}