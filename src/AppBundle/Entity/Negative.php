<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/7/16
 * Time: 8:18 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\NegativeRepository")
 * @ORM\Table(name="negative")
 * @GRID\Source(columns="id,negative")
 */
class Negative
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @GRID\Column(title="Negative", size="-1")
     */
    private $negative;


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
     * Set negative
     *
     * @param string $negative
     * @return Negative
     */
    public function setNegative($negative)
    {
        $this->negative = $negative;

        return $this;
    }

    /**
     * Get negative
     *
     * @return string 
     */
    public function getNegative()
    {
        return $this->negative;
    }
}
