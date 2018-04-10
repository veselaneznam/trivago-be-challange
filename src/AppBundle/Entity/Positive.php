<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/7/16
 * Time: 8:17 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PositiveRepository")
 * @ORM\Table(name="positive")
 * @GRID\Source(columns="id,positive")
 */
class Positive
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @GRID\Column(title="Positive", size="-1")
     */
    private $positive;

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
     * Set positive
     *
     * @param string $positive
     * @return Positive
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;

        return $this;
    }

    /**
     * Get positive
     *
     * @return string 
     */
    public function getPositive()
    {
        return $this->positive;
    }
}
