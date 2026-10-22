<?php

namespace App\Support;

/**
 * Dashboard keeps Livewire screens mounted (Alpine hide/show).
 * Call notifyListsChanged() after saving users / trades / companies / etc.
 * so other screens reload their select dropdowns without a full page refresh.
 */
class PortalRefresh
{
    public static function jsNotifyListsChanged(): string
    {
        return 'window.dispatchEvent(new CustomEvent("portal-data-changed"))';
    }
}
