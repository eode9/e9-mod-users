<?php

namespace E9\User\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use Doctrine\ODM\MongoDB\Mapping\Annotations\ReferenceMany;
use E9\Core\Document\AbstractDocument;

/**
 * @Document(repositoryClass="E9\User\Repository\UserRepository")
 * @Collection(name="mod_user")
 */
class User extends AbstractDocument
{
    /**
     * @var string
     * @Field(type="string")
     */
    public $fullName;

    /**
     * @var string
     * @Field(type="string")
     */
    public $email;

    /**
     * @var string
     * @Field(type="string")
     */
    public $password;

    /**
     * @var Group[]|ArrayCollection
     * @ReferenceMany(targetDocument="Group")
     */
    public $groups;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function jsonSerialize() :array
    {
        return array(
            'id' => $this->id,
            'fullname' => $this->fullName,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        );
    }
}
