<aside class="sidebar" :class="{ 'sidebar--open': sidebarOpen }">
    <a class="sidebar-brand" href="{{ route('dashboard') }}" aria-label="Federal Manpower Portal">
        <img src="{{ asset('images/federal-mark.png') }}" alt="Federal Group symbol" class="sidebar-brand__mark">
        <span class="sidebar-brand__copy">
            <strong>Federal</strong>
            <small>Manpower Portal</small>
        </span>
    </a>

    <nav class="sidebar-nav" aria-label="Main navigation">
        <p class="sidebar-nav__label">Workspace</p>
        @php
            $items = [
                ['overview', 'Overview', 'grid'],
                ['recruitment', 'Recruitment', 'briefcase'],
                ['candidates', 'Candidates', 'users'],
                ['workforce', 'Workforce', 'badge'],
                ['clients', 'Clients', 'building'],
                ['documents', 'Documents & Visas', 'document'],
            ];
        @endphp
        @foreach ($items as [$key, $label, $icon])
            <button class="nav-item" :class="{ 'nav-item--active': activeSection === '{{ $key }}' }" @click="setSection('{{ $key }}')" @mouseenter="$el.classList.add('nav-touched')">
                <span class="nav-item__icon">@include('partials.icon', ['name' => $icon])</span>
                <span>{{ $label }}</span>
                <span class="nav-item__indicator"></span>
            </button>
        @endforeach

        <p class="sidebar-nav__label sidebar-nav__label--spaced">Management</p>
        <button class="nav-item" :class="{ 'nav-item--active': activeSection === 'reports' }" @click="setSection('reports')" @mouseenter="$el.classList.add('nav-touched')">
            <span class="nav-item__icon">@include('partials.icon', ['name' => 'chart'])</span><span>Reports</span><span class="nav-item__indicator"></span>
        </button>
        <button class="nav-item" :class="{ 'nav-item--active': activeSection === 'settings' }" @click="setSection('settings')" @mouseenter="$el.classList.add('nav-touched')">
            <span class="nav-item__icon">@include('partials.icon', ['name' => 'settings'])</span><span>Settings</span><span class="nav-item__indicator"></span>
        </button>
    </nav>

    <div class="sidebar-support">
        <span class="sidebar-support__icon">@include('partials.icon', ['name' => 'headphones'])</span>
        <div><strong>Need assistance?</strong><small>Contact portal support</small></div>
        <span class="sidebar-support__arrow">›</span>
    </div>

    <div class="sidebar-user">
        <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
        <span class="sidebar-user__copy">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </span>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-user__logout-form">
            @csrf
            <button type="submit" class="sidebar-user__logout" title="Sign out">
                @include('partials.icon', ['name' => 'logout'])
            </button>
        </form>
    </div>
</aside>
