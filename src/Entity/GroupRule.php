<?php
namespace E9\User\Entity;

use E9\Core\Entity\AbstractEntity;
use App\Core\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="core_group_rule", uniqueConstraints={@ORM\UniqueConstraint(name="group_rule", columns={"group_id", "resource_id", "privilege_id"})}))
 */
class GroupRule extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="groupRule")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $group;

    /**
     * @ORM\ManyToOne(targetEntity="Resource", inversedBy="resource")
     * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
     */
    private $resource;

    /**
     * @ORM\ManyToOne(targetEntity="Privilege", inversedBy="privilege")
     * @ORM\JoinColumn(name="privilege_id", referencedColumnName="id")
     */
    private $privilege;

    public function __construct() {}

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    /**
     * Get group
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get resource
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Get privilege
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }
}
