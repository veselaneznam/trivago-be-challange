<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/2/16
 * Time: 4:01 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ReviewRepository")
 * @ORM\Table(name="review")
 * @GRID\Source(columns="id, review ,author, hotel.name ,scoreDescription, totalScore")
 */
class Review
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(title="ID", size="-1", visible=false, sortable=true)
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message = "Review can not be empty")
     * @GRID\Column(title="Review", size="-1")
     */
    private $review;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Author can not be empty")
     * @GRID\Column(title="Author", size="-1", sortable=true)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Hotel", inversedBy="hotel")
     * @ORM\JoinColumn(name="hotel_id", referencedColumnName="id")
     * @Assert\NotBlank(message = "Hotel can not be empty")
     * @GRID\Column(field="hotel.name", title="Hotel", joinType="inner")
     */
    private $hotel;

    /**
     * @ORM\Column(type="integer")
     * @GRID\Column(title="Total Score", size="-1", sortable=true)
     */
    private $totalScore;


    /**
     * @ORM\Column(type="text")
     * @GRID\Column(title="Score Description", size="-1")
     */
    private $scoreDescription;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set review
     *
     * @param string $review
     *
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * Get review
     *
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set author
     *
     * @param string $author
     *
     * @return Review
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set totalScore
     *
     * @param integer $totalScore
     *
     * @return Review
     */
    public function setTotalScore($totalScore)
    {
        $this->totalScore = $totalScore;

        return $this;
    }

    /**
     * Get totalScore
     *
     * @return integer
     */
    public function getTotalScore()
    {
        return $this->totalScore;
    }

    /**
     * Set hotel
     *
     * @param \AppBundle\Entity\Hotel $hotel
     * @return Review
     */
    public function setHotel(\AppBundle\Entity\Hotel $hotel = null)
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * Get hotel
     *
     * @return \AppBundle\Entity\Hotel 
     */
    public function getHotel()
    {
        return $this->hotel;
    }
  

    /**
     * Set score_description
     *
     * @param string $scoreDescription
     * @return Review
     */
    public function setScoreDescription($scoreDescription)
    {
        $this->scoreDescription = $scoreDescription;

        return $this;
    }

    /**
     * Get score_description
     *
     * @return string 
     */
    public function getScoreDescription()
    {
        return $this->scoreDescription;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'hotel' => $this->getHotel()->toArray(),
            'review' => $this->getReview(),
            'author' => $this->getAuthor(),
            'total_score' => (int) $this->getTotalScore(),
            'score_description' => $this->getScoreDescription()
        ];
    }
}
