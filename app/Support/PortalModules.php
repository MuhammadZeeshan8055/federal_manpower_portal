<?php

namespace App\Support;

use App\Models\User;

class PortalModules
{
    public static function forUser(?User $user = null): array
    {
        $user = $user ?? auth()->user();

        if (! $user) {
            return [];
        }

        $result = [];

        foreach (config('portal.modules', []) as $module) {
            $filtered = self::filterModule($module, $user);

            if ($filtered !== null) {
                $result[] = $filtered;
            }
        }

        return $result;
    }

    public static function mapForUser(?User $user = null): array
    {
        $map = [];

        foreach (self::forUser($user) as $module) {
            $map[$module['key']] = $module;
        }

        return $map;
    }

    private static function filterModule(array $module, User $user): ?array
    {
        $moduleKey = $module['key'] ?? '';

        if ($moduleKey === 'settings') {
            if ($user->canManageUsers()) {
                return $module;
            }

            return null;
        }

        if ($user->isAdmin()) {
            return $module;
        }

        $allowedFeatures = [];

        foreach ($module['children'] ?? [] as $feature) {
            if (! empty($feature['admin_only'])) {
                continue;
            }

            $featureKey = $feature['key'] ?? '';

            if ($user->canView($moduleKey, $featureKey)) {
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
