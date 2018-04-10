<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/23/16
 * Time: 2:07 PM
 */
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Hotel;
use AppBundle\Entity\Review;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadReviewData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $hotel = new Hotel();
        $hotel->setName('Paris');
        $manager->persist($hotel);
        $manager->flush();
        
        $review = new Review();
        $review->setHotel($hotel);
        $review->setReview('The hotel was nice and bad in the same time');
        $review->setAuthor('Vesela');
        $review->setTotalScore(0);
        $review->setScoreDescription('');
        $manager->persist($review);
        $manager->flush();
    }

    public function getOrder()
    {
        return 5;
    }
}