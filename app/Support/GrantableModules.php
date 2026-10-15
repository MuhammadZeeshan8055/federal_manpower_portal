<?php

namespace App\Support;

class GrantableModules
{
    // Modules you can tick in Manage Users (Settings is admin-only, skip it)
    public static function list(): array
    {
        $list = [];

        foreach (config('portal.modules', []) as $module) {
            if ($module['key'] === 'settings') {
                continue;
            }

            $list[] = [
                'key' => $module['key'],
                'title' => $module['title'],
            ];
        }

        return $list;
    }

    // Which feature keys to save for one module
    public static function featureKeysFor(string $moduleKey, string $role): array
    {
        $keys = [];

        foreach (config('portal.modules', []) as $module) {
            if ($module['key'] !== $moduleKey) {
                continue;
            }

            foreach ($module['children'] as $child) {
                // Normal features — always include
                if (empty($child['admin_only'])) {
                    $keys[] = $child['key'];
                    continue;
                }

                // admin_only: only give to HR on attendance
                if ($role === 'hr' && $moduleKey === 'attendance') {
                    $keys[] = $child['key'];
                }
            }
        }

        return $keys;
    }
}
