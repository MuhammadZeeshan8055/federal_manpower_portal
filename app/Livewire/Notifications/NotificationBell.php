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
