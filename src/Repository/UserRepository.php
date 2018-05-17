<?php

namespace E9\User\Repository;

use E9\Core\Repository\AbstractRepository;
use E9\User\Entity\User;

/**
 * Class Resource
 * @package App
 */
class UserRepository extends AbstractRepository
{
    /**
     * @param User $user
     * @param $password
     * @return User
     */
    public function setNewPassword(User $user, $password)
    {
        $user->setPassword($password);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }
 }
