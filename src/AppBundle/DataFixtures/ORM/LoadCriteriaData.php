<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/23/16
 * Time: 2:07 PM
 */
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Negative;
use AppBundle\Entity\Positive;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Criteria;

class LoadCriteriaData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $criteria = new Criteria();
        $criteria->setName('room');
        $criteria->setAlternativeName('room, apartment');
        $manager->persist($criteria);
        $manager->flush();

        $negative = new Negative();
        $negative->setNegative('bad');
        $manager->persist($negative);
        $manager->flush();

        $positive = new Positive();
        $positive->setPositive('nice');
        $manager->persist($positive);
        $manager->flush();
    }


    public function getOrder()
    {
        return 1;
    }
}