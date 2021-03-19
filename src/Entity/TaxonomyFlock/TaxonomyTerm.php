<?php

namespace App\Entity\TaxonomyFlock;

use App\Entity\AdminFlock\TimestampableInterface;
use App\Entity\AdminFlock\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *   "TaxonomyTerm" = "TaxonomyTerm",
 * })
 * @ORM\Entity(repositoryClass="App\Repository\TaxonomyFlock\TaxonomyTermRepository")
 */
class TaxonomyTerm implements TimestampableInterface
{
    use TimestampableTrait;

    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="term", type="string", length=255)
     * @Assert\NotBlank(message="Campo obbligatorio")
     */
    protected $term;

    /**
     * @var string
     *
     * @ORM\Column(name="machine_term", type="string", length=255, nullable=true)
     */
    protected $machineTerm;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function __toString()
    {
        return $this->term;
    }

    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set term
     *
     * @param string $term
     * @return TaxonomyTerm
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }


    /**
     * @return string
     */
    public function getMachineTerm()
    {
        return $this->machineTerm;
    }

    /**************************************/
    /* END                                */
    /**************************************/
}

