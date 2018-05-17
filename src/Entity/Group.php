<?php

namespace E9\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use E9\Core\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="core_groups", uniqueConstraints={@ORM\UniqueConstraint(name="group_slug", columns={"slug"})}))
 */
class Group extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $slug;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="GroupRule", mappedBy="group")
     */
    private $rules;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->slug;
    }

    /**
     * @return ArrayCollection
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
