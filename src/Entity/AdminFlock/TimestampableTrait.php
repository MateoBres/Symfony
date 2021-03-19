<?php

namespace App\Entity\AdminFlock;

use Doctrine\ORM\Mapping as Orm;
use Gedmo\Mapping\Annotation as Gedmo;


trait TimestampableTrait
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFlock\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $createdBy;

    /**
     * @var \DateTime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="App\Entity\UserFlock\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $updatedBy;

    /**
     * @var \DateTime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**************************************/
    /* CUSTOM CODE                        */
    /**************************************/

    public function getFullClassName()
    {
        return str_replace('Proxies\\__CG__\\', '', get_class($this));
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function getTimestampableDetail()
    {
        $res = 'creato';
        if ($this->getCreatedBy()) $res .= ' da <b>' . $this->getCreatedBy() . '</b>';
        if ($this->getCreatedAt()) $res .= '<br/>il ' . $this->getCreatedAt()->format('d/m/Y H:i:s');

        if ($this->getCreatedAt() != $this->getUpdatedAt()) {
            $res .= '<br/><br/>modificato';
            if ($this->getUpdatedBy()) $res .= ' da <b>' . $this->getUpdatedBy() . '</b>';
            if ($this->getUpdatedAt()) $res .= '<br/>il ' . $this->getUpdatedAt()->format('d/m/Y H:i:s');
        }

        return $res;
    }
    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/


    /**
     * Set createdBy
     *
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return $this
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**************************************/
    /* END                                */
    /**************************************/
} 