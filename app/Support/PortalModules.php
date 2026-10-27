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

        // Staff / HR: only features they can view
        $allowedFeatures = [];

        foreach ($module['children'] ?? [] as $feature) {
            // admin_only: skip for normal staff; HR may see attendance ones
            if (! empty($feature['admin_only'])) {
                if ($moduleKey !== 'attendance' || ! $user->isHr()) {
                    continue;
                }
            }

            // Reminders list uses the same access as Visitors list
            if ($moduleKey === 'visitors' && $feature['key'] === 'reminders') {
                if ($user->canView('visitors', 'list') || $user->canView('visitors', 'reminders')) {
                    $allowedFeatures[] = $feature;
                }

                continue;
            }

            // Batches uses Clients list access when batches permission is not set yet
            if ($moduleKey === 'clients' && $feature['key'] === 'batches') {
                if ($user->canView('clients', 'batches')
                    || $user->canView('clients', 'list')
                    || $user->canManage('clients', 'list')) {
                    $allowedFeatures[] = $feature;
                }

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
