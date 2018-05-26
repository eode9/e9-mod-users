<?php

namespace E9\User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use E9\Core\Document\AbstractDocument;

/**
 * @Document(repositoryClass="E9\User\Repository\PrivilegeRepository")
 * @Collection(name="mod_user_privilege")
 */
class Privilege extends AbstractDocument
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

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
