<?php

namespace App\Security;

use App\Entity\User;
use App\Helper\AppHelper;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * Checks User roles respecting the hierarchy
 */
class RoleChecker
{
    public function __construct(private RoleHierarchyInterface $roleHierarchy) {}

    /**
     * Return isGranted when passing a User
     */
    public function isGranted(User $user, string $role): bool
    {
        return in_array($role, $this->getRoles($user), true);
    }

    /**
     * Returns an array of all the roles a User has and inherits
     */
    public function getRoles(User $user): array
    {
        return $this->roleHierarchy->getReachableRoleNames($user->getRoles());
    }

    /**
     * Returns an array of the role and all roles above it with hierarchy
     */
    public function getRoleHierarchyAbove(string $role): array
    {
        $result = [];
        $appRoles = User::ROLES;
        foreach ($appRoles as $appRole) {
            if (in_array($role, $this->roleHierarchy->getReachableRoleNames([$appRole]))) {
                $result[] = $appRole;
            }
        }
        return $result;
    }
}