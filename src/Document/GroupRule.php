<?php

namespace E9\User\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations\ReferenceOne;
use E9\Core\Document\AbstractDocument;

/**
 * @Document(repositoryClass="E9\User\Repository\GroupRuleRepository")
 * @Collection(name="mod_user_rule")
 */
class GroupRule extends AbstractDocument
{
    /**
     * @var Group
     * @ReferenceOne(targetDocument="Group")
     */
    public $group;

    /**
     * @var Resource
     * @ReferenceOne(targetDocument="Resource")
     */
    public $resource;

    /**
     * @var Privilege
     * @ReferenceOne(targetDocument="Privilege")
     */
    public $privilege;

    public function jsonSerialize()
    {
        return [
          'id' => $this->id,
          'created_at' => $this->id,
          'updated_at' => $this->id,
          'group' => $this->group->id,
          'resource' => $this->resource->id,
          'privilege' => $this->privilege->id
        ];
    }
}
