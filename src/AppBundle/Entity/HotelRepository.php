<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HotelRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HotelRepository extends EntityRepository
{

    /**
     * @param string $hotelName
     * @return Hotel
     */
    public function getHotelByName($hotelName)
    {
        return $this->findOneBy(array('name' => $hotelName));
    }
}
