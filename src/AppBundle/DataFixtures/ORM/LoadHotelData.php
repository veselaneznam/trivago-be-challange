<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/23/16
 * Time: 2:07 PM
 */
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Hotel;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadHotelData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $hotel = new Hotel();
        $hotel->setName('Hilton');
        $manager->persist($hotel);
        $manager->flush();
    }


    public function getOrder()
    {
        return 4;
    }
}