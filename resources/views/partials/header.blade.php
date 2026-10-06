<header class="topbar">
    <div class="topbar__title-wrap">
        <button class="menu-button" @click="sidebarOpen = !sidebarOpen" aria-label="Toggle navigation">@include('partials.icon', ['name' => 'menu'])</button>
        <div>
            <p class="breadcrumb">Federal Manpower <span>/</span> <span x-text="sectionTitles[activeSection][0]"></span></p>
            <h1 class="page-title" x-text="sectionTitles[activeSection][0]"></h1>
        </div>
    </div>
    <div class="topbar__actions">
        <label class="search-box">
            @include('partials.icon', ['name' => 'search'])
            <input type="search" placeholder="Search candidates, clients...">
            <kbd>⌘ K</kbd>
        </label>
        <button class="icon-button notification-button" aria-label="Notifications">@include('partials.icon', ['name' => 'bell'])<span></span></button>
        <div class="profile" @click.outside="profileOpen = false">
            <button class="profile__button" @click="profileOpen = !profileOpen">
                <span class="avatar avatar--header">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                <span class="profile__copy">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ auth()->user()->email }}</small>
                </span>
                <span class="profile__chevron">⌄</span>
            </button>
            <div class="profile-menu" x-cloak x-show="profileOpen" x-transition>
                <a href="{{ route('profile.edit') }}">My profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Sign out</button>
                </form>
            </div>
        </div>
    </div>
</header>
