<?php
/**
 * Created by PhpStorm.
 * User: vesela
 * Date: 4/2/16
 * Time: 4:22 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CriteriaRepository")
 * @ORM\Table(name="criteria")
 * @GRID\Source(columns="id, name, alternativeName")
 * @UniqueEntity(
 *     fields={"name"},
 *     errorPath="name",
 *     message="This criteria name is already in use"
 * )
 */
class Criteria
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(title="ID", size="-1", visible=false)
     */
    private $id;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message = "Name can not ne empty")
     * @GRID\Column(title="Criteria Name", size="-1")
     */
    private $name;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "Name can not ne empty")
     * @GRID\Column(title="Alternative Name", size="-1", inputType="text")
     */
    private $alternativeName;
    
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
     * @return Criteria
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
     * Set alternativeName
     *
     * @param string $alternativeName
     *
     * @return Criteria
     */
    public function setAlternativeName($alternativeName)
    {
        $this->alternativeName = $alternativeName;

        return $this;
    }

    /**
     * Get alternativeName
     *
     * @return string
     */
    public function getAlternativeName()
    {
        return $this->alternativeName;
    }
}
