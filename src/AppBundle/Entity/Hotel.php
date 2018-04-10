<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/2/16
 * Time: 4:22 PM
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\HotelRepository")
 * @ORM\Table(name="hotel")
 * @GRID\Source(columns="id,name")
 */
class Hotel
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message = "Hotel Name can not be empty")
     * @GRID\Column(title="Hotel Name", size="-1")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Review", mappedBy="hotel")
     */
    private $hotels;

    public function __construct()
    {
        $this->hotels = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Hotel
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Add hotels
     *
     * @param \AppBundle\Entity\Review $hotels
     * @return Hotel
     */
    public function addHotel(\AppBundle\Entity\Review $hotels)
    {
        $this->hotels[] = $hotels;

        return $this;
    }

    /**
     * Remove hotels
     *
     * @param \AppBundle\Entity\Review $hotels
     */
    public function removeHotel(\AppBundle\Entity\Review $hotels)
    {
        $this->hotels->removeElement($hotels);
    }

    /**
     * Get hotels
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getHotels()
    {
        return $this->hotels;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName()
        ];
    }
}
