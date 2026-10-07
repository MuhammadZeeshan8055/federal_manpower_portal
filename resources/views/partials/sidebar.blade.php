<aside
    class="sidebar"
    :class="{ 'sidebar--open': sidebarOpen, 'sidebar--collapsed': sidebarCollapsed }"
    :aria-expanded="(!sidebarCollapsed).toString()"
>
    <a class="sidebar-brand" href="{{ route('dashboard') }}" aria-label="Federal Manpower Portal">
        <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol" class="sidebar-brand__mark">
        <span class="sidebar-brand__copy">
            <strong>Federal</strong>
            <small>Group Of Companies</small>
        </span>
    </a>

    <nav class="sidebar-nav" aria-label="Main navigation">
        <p class="sidebar-nav__label">Workspace</p>

        <button
            type="button"
            class="nav-item"
            :class="{ 'nav-item--active': screen === 'home' }"
            @click="goHome()"
            @mouseenter="$el.classList.add('nav-touched')"
        >
            <span class="nav-item__icon">@include('partials.icon', ['name' => 'grid'])</span>
            <span class="nav-item__label">Operations Overview</span>
            <span class="nav-item__indicator"></span>
        </button>

        <template x-if="screen !== 'home'">
            <div
                class="sidebar-module"
                x-transition:enter="sidebar-anim-in"
                x-transition:enter-start="sidebar-anim-in-start"
                x-transition:enter-end="sidebar-anim-in-end"
                x-transition:leave="sidebar-anim-out"
                x-transition:leave-start="sidebar-anim-out-start"
                x-transition:leave-end="sidebar-anim-out-end"
            >
                <p
                    class="sidebar-nav__label sidebar-nav__label--spaced sidebar-module__item"
                    style="--i: 0"
                    x-text="moduleTitle"
                ></p>

                <button
                    type="button"
                    class="nav-item sidebar-module__item"
                    style="--i: 1"
                    :class="{ 'nav-item--active': screen === 'module' }"
                    @click="goModule()"
                    @mouseenter="$el.classList.add('nav-touched')"
                >
                    <span class="nav-item__icon">@include('partials.icon', ['name' => 'chart'])</span>
                    <span class="nav-item__label">Module Dashboard</span>
                    <span class="nav-item__indicator"></span>
                </button>

                <template x-for="(feature, index) in features" :key="feature.key">
                    <button
                        type="button"
                        class="nav-item sidebar-module__item"
                        :style="'--i:' + (index + 2)"
                        :class="{ 'nav-item--active': featureKey === feature.key }"
                        @click="openFeature(feature.key)"
                        @mouseenter="$el.classList.add('nav-touched')"
                    >
                        <span class="nav-item__icon">@include('partials.icon', ['name' => 'document'])</span>
                        <span class="nav-item__label" x-text="feature.label"></span>
                        <span class="nav-item__indicator"></span>
                    </button>
                </template>
            </div>
        </template>
    </nav>

    <div class="sidebar-user">
        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
        <span class="sidebar-user__copy">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->roleLabel() }}</small>
        </span>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-user__logout-form">
            @csrf
            <button type="submit" class="sidebar-user__logout" title="Sign out">
                @include('partials.icon', ['name' => 'logout'])
            </button>
        </form>
    </div>
</aside>
