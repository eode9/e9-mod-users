<?php

namespace E9\User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Field;
use E9\Core\Document\AbstractDocument;

/**
 * @Document(
 *     collection={"name"="user_group_privilege"},
 *     repositoryClass="E9\User\Repository\PrivilegeRepository"
 * )
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
