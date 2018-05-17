<?php

$container = $app->getContainer();

/**
 * Return the session manager component
 * @param $c
 * @return \E9\User\Entity\User
 */
$container['user'] = function ($c) {
    $user = json_decode($c['session']->get('user'));
    if ($user) {
        return $c->get('em')->getRepository(\E9\User\Entity\User::class)->find($user);
    } else {
        return null;
    }
};

/**
 * Return the ACL manager component
 * @param $c
 * @return \SimpleAcl\Acl
 */
$container['acl'] = function ($c) {
    $user = $c->get('user');

    $acl = new \SimpleAcl\Acl();

    if ($user) {
        $userRole = new \SimpleAcl\Role($user->id);
        $userGroups = $user->groups;

        foreach ($userGroups as $group) {
            foreach ($group->getRules() as $rule) {
                $resource = new \SimpleAcl\Resource($rule->getResource()->getSlug());
                $acl->addRule($userRole, $resource, $rule->getPrivilege()->getSlug(), true);
            }
        }
    }
    return $acl;
};