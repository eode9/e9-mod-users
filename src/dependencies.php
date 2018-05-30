<?php

$container = $app->getContainer();

/**
 * Return the session manager component
 * @param $c
 * @return \E9\User\Document\User
 */
$container['user'] = function ($c) {
    $userId = json_decode($c['session']->get('user_id'));
    if ($userId) {
        return $c->get('dm')->getRepository(\E9\User\Document\User::class)->find($userId);
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
