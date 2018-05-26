<?php

namespace E9\User\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\ReferenceMany;
use E9\Core\Document\AbstractDocument;

/**
 * @Document(repositoryClass="E9\User\Repository\GroupRepository")
 * @Collection(name="mod_user_group")
 */
class Group extends AbstractDocument
{
    /**
     * @var string
     * @Field(type="string")
     */
    public $name;

    /**
     * @var string
     * @Field(type="string")
     */
    public $slug;

    /**
     * @var string
     * @Field(type="string")
     */
    public $description;

    /**
     * @var User[]|ArrayCollection
     * @ReferenceMany(targetDocument="User")
     */
    public $users;

    /**
     * @var GroupRule|ArrayCollection
     * @ReferenceMany(targetDocument="GroupRule")
     */
    public $groupRules;

    public function __construct()
    {
        parent::__construct();

        $this->users = new ArrayCollection();
        $this->groupRules = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
