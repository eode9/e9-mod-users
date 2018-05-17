<?php

namespace E9\User\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use E9\Core\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="core_users", indexes={@ORM\Index(name="idx_email", columns={"email"})})
 * @ORM\HasLifecycleCallbacks()
 */
class User extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="full_name", length=64, nullable=true)
     */
    public $fullName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     *
     * @Assert\NotBlank(message="Please provide email")
     * @Assert\Email(message="Please provide correct email")
     */
    public $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    public $password;

    /**
     * @var Group
     *
     * @ORM\ManyToMany(targetEntity="App\Core\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="core_users_groups")
     */
    public $groups;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'uuid' => $this->id,
            'fullname' => $this->fullName,
            'email' => $this->email,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        );
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        if (strlen($password) >= 6) {
            $this->password = md5(getenv('APP_SALT') . $password);
        }
        return $this;
    }
}
