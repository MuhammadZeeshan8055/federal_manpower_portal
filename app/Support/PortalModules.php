<?php

namespace App\Support;

use App\Models\User;

class PortalModules
{
    // Modules the logged-in user can see (list)
    public static function forUser($user = null)
    {
        if (! $user) {
            $user = auth()->user();
        }

        if (! $user) {
            return [];
        }

        $result = [];

        foreach (config('portal.modules', []) as $module) {
            $allowed = self::moduleForUser($module, $user);

            if ($allowed) {
                $result[] = $allowed;
            }
        }

        return $result;
    }

    // Same modules, but keyed by module key (easy lookup)
    public static function mapForUser($user = null)
    {
        $map = [];

        foreach (self::forUser($user) as $module) {
            $map[$module['key']] = $module;
        }

        return $map;
    }

    private static function moduleForUser($module, User $user)
    {
        $moduleKey = $module['key'];

        // Settings only for people who manage users
        if ($moduleKey === 'settings') {
            if ($user->canManageUsers()) {
                return $module;
            }

            return null;
        }

        // Admin sees everything
        if ($user->isAdmin()) {
            return $module;
        }

        // Staff: only features they can view
        $allowedFeatures = [];

        foreach ($module['children'] ?? [] as $feature) {
            if (! empty($feature['admin_only'])) {
                continue;
            }

            if ($user->canView($moduleKey, $feature['key'])) {
                $allowedFeatures[] = $feature;
            }
        }

        if (count($allowedFeatures) === 0) {
            return null;
        }

        $module['children'] = $allowedFeatures;

        return $module;
    }
}
