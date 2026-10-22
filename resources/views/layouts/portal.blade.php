<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#145f90">
    @include('partials.theme-script')
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('images/favicon.png') }}">
    <title>@yield('title', 'Dashboard') — Federal Manpower Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ filemtime(public_path('css/portal.css')) }}">
    @livewireStyles
</head>
<body
    class="portal-body"
    x-data="portalApp()"
    :class="{ 'nav-open': sidebarOpen, 'sidebar-collapsed': sidebarCollapsed }"
>
    <div class="portal-shell">
        @include('partials.sidebar')

        <div class="portal-main">
            @include('partials.header')
            <main class="portal-content">
                @yield('content')
            </main>
        </div>
    </div>

    <button
        class="nav-overlay"
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        aria-label="Close navigation"
    ></button>

    <script>
        /*
         * FAST SPA (no full page reload)
         *
         * Debug map:
         *   1. Wrong screen/title  → show()
         *   2. Wrong URL           → setUrl()
         *   3. Wrong count numbers → refreshStats() + ModuleStats.php
         *   4. First load from URL → DashboardController + show()
         */
        function portalApp() {
            return {
                sidebarOpen: false,
                sidebarCollapsed: false,
                profileOpen: false,
                darkMode: document.documentElement.dataset.theme === 'dark',

                screen: 'home',
                moduleKey: '',
                moduleTitle: '',
                moduleDescription: '',
                features: [],
                featureKey: '',
                featureTitle: '',

                modules: @js($modulesMap ?? []),
                startModule: @js($startModule ?? ''),
                startFeature: @js($startFeature ?? ''),

                init() {
                    var self = this;

                    this.applyTheme(this.darkMode ? 'dark' : 'light', false);
                    this.loadSidebarCollapsed();

                    // Dashboard SPA only — profile page keeps its own URL
                    if (document.querySelector('.dashboard-view')) {
                        this.show(this.startModule, this.startFeature, false);
                        this.readStatusFromUrl();

                        window.addEventListener('popstate', function () {
                            self.showFromBrowserUrl();
                        });

                        document.addEventListener('livewire:initialized', function () {
                            self.refreshStats();
                        });

                        // Bell / other screens → navigate without reload
                        window.addEventListener('portal-go', function (event) {
                            var detail = event.detail || {};
                            var moduleKey = detail.module || '';
                            var featureKey = detail.feature || '';
                            var visitorId = detail.visitorId || null;

                            self.show(moduleKey, featureKey, true);

                            if (moduleKey === 'visitors' && featureKey === 'reminders' && visitorId) {
                                self.$nextTick(function () {
                                    window.dispatchEvent(new CustomEvent('open-visitor-reminder', {
                                        detail: { visitorId: visitorId },
                                    }));
                                });
                            }
                        });

                        // After add/edit user, trade, company, etc. — refresh all selects
                        window.addEventListener('portal-data-changed', function () {
                            self.refreshAllLivewire();
                        });
                    }
                },

                // Livewire screens stay mounted; reload them so dropdown lists stay fresh
                refreshAllLivewire() {
                    if (! window.Livewire || typeof Livewire.all !== 'function') {
                        return;
                    }

                    Livewire.all().forEach(function (component) {
                        try {
                            if (component && typeof component.$refresh === 'function') {
                                component.$refresh();
                            }
                        } catch (error) {
                            // ignore single-component refresh errors
                        }
                    });
                },

                // -------------------------------------------------
                // MAIN FUNCTION — everything goes through here
                // moduleKey = '' means home
                // featureKey = '' means module dashboard
                // changeUrl = true updates the address bar (no reload)
                // -------------------------------------------------
                show(moduleKey, featureKey, changeUrl) {
                    var module;
                    var i;
                    var feature;
                    var path;

                    moduleKey = moduleKey || '';
                    featureKey = featureKey || '';

                    if (changeUrl === undefined) {
                        changeUrl = true;
                    }

                    // Profile (and other non-dashboard pages): full load into SPA
                    if (! document.querySelector('.dashboard-view')) {
                        path = '/dashboard';
                        if (moduleKey) {
                            path = path + '/' + moduleKey;
                            if (featureKey) {
                                path = path + '/' + featureKey;
                            }
                        }
                        window.location.href = path;
                        return;
                    }

                    // HOME
                    if (moduleKey === '' || !this.modules[moduleKey]) {
                        this.screen = 'home';
                        this.moduleKey = '';
                        this.moduleTitle = '';
                        this.moduleDescription = '';
                        this.features = [];
                        this.featureKey = '';
                        this.featureTitle = '';
                        this.sidebarOpen = false;
                        window.scrollTo(0, 0);
                        this.refreshStats();

                        if (changeUrl) {
                            this.setUrl();
                        }

                        return;
                    }

                    module = this.modules[moduleKey];

                    this.moduleKey = moduleKey;
                    this.moduleTitle = module.title;
                    this.moduleDescription = module.description;
                    this.features = module.children ? module.children : [];
                    this.sidebarOpen = false;
                    window.scrollTo(0, 0);

                    // MODULE dashboard (no feature)
                    if (featureKey === '') {
                        this.screen = 'module';
                        this.featureKey = '';
                        this.featureTitle = '';
                        this.refreshStats();

                        if (changeUrl) {
                            this.setUrl();
                        }

                        return;
                    }

                    // FEATURE screen
                    feature = null;

                    for (i = 0; i < this.features.length; i++) {
                        if (this.features[i].key === featureKey) {
                            feature = this.features[i];
                            break;
                        }
                    }

                    if (!feature) {
                        // Unknown feature → show module dashboard
                        this.show(moduleKey, '', changeUrl);
                        return;
                    }

                    this.screen = 'feature';
                    this.featureKey = featureKey;
                    this.featureTitle = feature.label;
                    this.refreshStats();

                    if (changeUrl) {
                        this.setUrl();
                    }

                    // Livewire screens stay mounted (Alpine x-show) — reload lists on open
                    var app = this;
                    this.$nextTick(function () {
                        window.dispatchEvent(new CustomEvent('portal-feature-shown', {
                            detail: {
                                module: moduleKey,
                                feature: featureKey,
                            },
                        }));
                        app.refreshAllLivewire();
                    });
                },

                // Address bar only (page does NOT reload)
                setUrl() {
                    var url = '{{ url('/dashboard') }}';

                    if (this.moduleKey !== '') {
                        url = url + '/' + this.moduleKey;
                    }

                    if (this.screen === 'feature' && this.featureKey !== '') {
                        url = url + '/' + this.featureKey;
                    }

                    history.pushState({}, '', url);
                },

                // Read /dashboard/clients/list from the browser
                showFromBrowserUrl() {
                    var parts = window.location.pathname.split('/');
                    var moduleKey = '';
                    var featureKey = '';
                    var i;

                    for (i = 0; i < parts.length; i++) {
                        if (parts[i] === 'dashboard') {
                            moduleKey = parts[i + 1] || '';
                            featureKey = parts[i + 2] || '';
                            break;
                        }
                    }

                    this.show(moduleKey, featureKey, false);
                    this.readStatusFromUrl();
                },

                // Tell Livewire to show counts for this module
                refreshStats() {
                    if (typeof Livewire === 'undefined') {
                        return;
                    }

                    Livewire.dispatch('refresh-module-stats', { key: this.moduleKey || '' });
                },

                // Buttons / clicks
                goHome() {
                    this.show('', '', true);
                },

                openModule(key) {
                    this.show(key, '', true);
                },

                goModule() {
                    this.show(this.moduleKey, '', true);
                },

                openFeature(key, options) {
                    options = options || {};

                    this.show(this.moduleKey, key, true);

                    // Status card → filter All Clients list
                    if (this.moduleKey === 'clients' && key === 'list') {
                        this.filterClients(
                            options.processStatusId ? String(options.processStatusId) : '',
                            options.label || ''
                        );
                    }
                },

                openClientStat(stat) {
                    if (!stat || !stat.clickable) {
                        return;
                    }

                    this.openFeature('list', {
                        processStatusId: stat.process_status_id || '',
                        label: stat.label || '',
                    });
                },

                filterClients(statusId, label) {
                    if (label) {
                        this.featureTitle = label;
                    }

                    this.$nextTick(function () {
                        window.dispatchEvent(new CustomEvent('clients-filter-status', {
                            detail: {
                                id: statusId || '',
                                label: label || '',
                            },
                        }));
                    });
                },

                readStatusFromUrl() {
                    var params;
                    var statusId;
                    var label;

                    if (this.moduleKey !== 'clients' || this.featureKey !== 'list') {
                        return;
                    }

                    params = new URLSearchParams(window.location.search);
                    statusId = params.get('status') || '';
                    label = params.get('label') || '';

                    if (statusId || label) {
                        this.filterClients(statusId, label);
                    }
                },

                toggleSidebar() {
                    if (window.innerWidth <= 820) {
                        this.sidebarOpen = !this.sidebarOpen;
                        return;
                    }

                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    this.saveSidebarCollapsed();
                },

                saveSidebarCollapsed() {
                    try {
                        localStorage.setItem('federal_sidebar_collapsed', this.sidebarCollapsed ? '1' : '0');
                    } catch (error) {
                    }
                },

                loadSidebarCollapsed() {
                    try {
                        this.sidebarCollapsed = localStorage.getItem('federal_sidebar_collapsed') === '1';
                    } catch (error) {
                        this.sidebarCollapsed = false;
                    }
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    this.applyTheme(this.darkMode ? 'dark' : 'light', true);
                },

                applyTheme(theme, persist) {
                    document.documentElement.dataset.theme = theme;
                    document.documentElement.style.colorScheme = theme;

                    var themeColor = document.querySelector('meta[name="theme-color"]');
                    if (themeColor) {
                        themeColor.setAttribute('content', theme === 'dark' ? '#0b1520' : '#145f90');
                    }

                    if (persist) {
                        try {
                            localStorage.setItem('federal_theme', theme);
                        } catch (error) {
                        }
                    }
                },

                pageTitle() {
                    if (window.location.pathname.indexOf('/profile') === 0) {
                        return 'My profile';
                    }

                    if (this.screen === 'feature') {
                        return this.featureTitle;
                    }

                    if (this.screen === 'module') {
                        return this.moduleTitle;
                    }

                    return 'Operations Overview';
                },
            };
        }
    </script>
    @livewireScripts
</body>
</html>
