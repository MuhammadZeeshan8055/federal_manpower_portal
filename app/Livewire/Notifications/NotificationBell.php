<?php

namespace App\Livewire\Notifications;

use App\Models\UserNotification;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function toggleOpen(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function markAsRead(int $id): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $row = UserNotification::query()
            ->where('user_id', $user->id)
            ->whereKey($id)
            ->first();

        if (! $row) {
            return;
        }

        if (! $row->read_at) {
            $row->read_at = now();
            $row->save();
        }
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        UserNotification::query()
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function openNotification(int $id): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $row = UserNotification::query()
            ->where('user_id', $user->id)
            ->whereKey($id)
            ->first();

        if (! $row) {
            return;
        }

        if (! $row->read_at) {
            $row->read_at = now();
            $row->save();
        }

        $this->open = false;

        // Visitor reminders → Reminders list
        if (in_array($row->type, ['visitor_reminder', 'visitor_reminder_due'], true)) {
            $visitorId = (int) explode('-', (string) $row->feature_key)[0];

            $this->js(
                'window.dispatchEvent(new CustomEvent("portal-go", { detail: {'
                .' module: "visitors",'
                .' feature: "reminders",'
                .' visitorId: '.($visitorId > 0 ? $visitorId : 'null')
                .' }}));'
            );

            return;
        }

        // New leave request → Leave Approvals (for HR / admin)
        if ($row->type === 'leave_request') {
            $this->js(
                'window.dispatchEvent(new CustomEvent("portal-go", { detail: {'
                .' module: "attendance",'
                .' feature: "leave-approvals"'
                .' }}));'
            );

            return;
        }

        // Leave approved / rejected → Apply Leave (own history)
        if (in_array($row->type, ['leave_approved', 'leave_rejected'], true)) {
            $this->js(
                'window.dispatchEvent(new CustomEvent("portal-go", { detail: {'
                .' module: "attendance",'
                .' feature: "apply-leave"'
                .' }}));'
            );
        }
    }

    public function render()
    {
        $user = auth()->user();
        $items = collect();
        $unreadCount = 0;

        if ($user) {
            $items = UserNotification::query()
                ->where('user_id', $user->id)
                ->orderByDesc('id')
                ->limit(20)
                ->get();

            $unreadCount = UserNotification::query()
                ->where('user_id', $user->id)
                ->whereNull('read_at')
                ->count();
        }

        return view('livewire.notifications.notification-bell', [
            'items' => $items,
            'unreadCount' => $unreadCount,
        ]);
    }
}
