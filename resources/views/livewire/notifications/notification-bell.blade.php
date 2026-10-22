<div
    class="notify-bell"
    wire:poll.5s
    @click.outside="$wire.close()"
    @portal-feature-shown.window="$wire.$refresh()"
    @portal-notify-refresh.window="$wire.$refresh()"
>
    <button
        type="button"
        class="notify-bell__button"
        wire:click="toggleOpen"
        aria-expanded="{{ $open ? 'true' : 'false' }}"
        aria-label="Notifications"
        title="Notifications"
    >
        @include('partials.icon', ['name' => 'bell'])
        @if ($unreadCount > 0)
            <span class="notify-bell__badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    @if ($open)
        <div class="notify-bell__menu" wire:key="notify-menu">
            <div class="notify-bell__head">
                <strong>Notifications</strong>
                @if ($unreadCount > 0)
                    <button type="button" class="text-button" wire:click="markAllAsRead">
                        Mark all read
                    </button>
                @endif
            </div>

            <div class="notify-bell__list">
                @forelse ($items as $item)
                    <button
                        type="button"
                        class="notify-bell__item {{ $item->read_at ? '' : 'notify-bell__item--unread' }}"
                        wire:click="openNotification({{ $item->id }})"
                        wire:key="notify-{{ $item->id }}"
                    >
                        <span class="notify-bell__item-title">{{ $item->title }}</span>
                        @if ($item->body)
                            <span class="notify-bell__item-body">{{ $item->body }}</span>
                        @endif
                        <span class="notify-bell__item-time">{{ $item->created_at?->diffForHumans() }}</span>
                    </button>
                @empty
                    <p class="notify-bell__empty">No notifications yet.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
