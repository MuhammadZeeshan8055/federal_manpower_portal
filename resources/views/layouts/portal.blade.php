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
        // Simple portal app
        // URLs:
        //   /dashboard
        //   /dashboard/clients
        //   /dashboard/clients/list
        // Controller: DashboardController@index
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
                    this.startFromServer();

                    window.addEventListener('popstate', function () {
                        self.startFromBrowserUrl();
                    });

                    document.addEventListener('livewire:initialized', function () {
                        self.refreshModuleStats();
                    });
                },

                refreshModuleStats() {
                    if (typeof Livewire === 'undefined') {
                        return;
                    }

                    Livewire.dispatch('refresh-module-stats', { key: this.moduleKey || '' });
                },

                // Change address bar only (page does not reload)
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

                // First load: use values from DashboardController
                startFromServer() {
                    if (this.startModule === '' || !this.modules[this.startModule]) {
                        this.goHome(false);
                        return;
                    }

                    this.openModule(this.startModule, false);

                    if (this.startFeature !== '') {
                        this.openFeature(this.startFeature, {}, false);
                    }
                },

                // Browser back / forward button
                startFromBrowserUrl() {
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

                    if (moduleKey === '' || !this.modules[moduleKey]) {
                        this.goHome(false);
                        return;
                    }

                    this.openModule(moduleKey, false);

                    if (featureKey !== '') {
                        this.openFeature(featureKey, {}, false);
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
                        // Preference still applies for this page if storage is unavailable.
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
                            // The selected theme still applies for this page if storage is unavailable.
                        }
                    }
                },

                pageTitle() {
                    if (this.screen === 'feature') {
                        return this.featureTitle;
                    }
                    if (this.screen === 'module') {
                        return this.moduleTitle;
                    }
                    return 'Operations Overview';
                },

                // Open a module (example: clients)
                // changeUrl = false when loading from server / back button
                openModule(key, changeUrl) {
                    var module = this.modules[key];

                    if (!module) {
                        return;
                    }

                    if (changeUrl === undefined) {
                        changeUrl = true;
                    }

                    this.screen = 'module';
                    this.moduleKey = key;
                    this.moduleTitle = module.title;
                    this.moduleDescription = module.description;
                    this.features = module.children ? module.children : [];
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.sidebarOpen = false;
                    window.scrollTo(0, 0);
                    this.refreshModuleStats();

                    if (changeUrl) {
                        this.setUrl();
                    }
                },

                // Open a feature (example: list, create)
                openFeature(key, options, changeUrl) {
                    var i;
                    var feature = null;
                    var statusId = '';

                    options = options || {};

                    if (changeUrl === undefined) {
                        changeUrl = true;
                    }

                    for (i = 0; i < this.features.length; i++) {
                        if (this.features[i].key === key) {
                            feature = this.features[i];
                            break;
                        }
                    }

                    if (!feature) {
                        return;
                    }

                    this.screen = 'feature';
                    this.featureKey = key;
                    this.featureTitle = feature.label;
                    this.sidebarOpen = false;
                    window.scrollTo(0, 0);

                    if (changeUrl) {
                        this.setUrl();
                    }

                    // Status cards on Clients module → filter All Clients list
                    if (this.moduleKey === 'clients' && key === 'list') {
                        statusId = options.processStatusId ? String(options.processStatusId) : '';

                        if (statusId && options.label) {
                            this.featureTitle = options.label;
                        }

                        this.$nextTick(function () {
                            window.dispatchEvent(new CustomEvent('clients-filter-status', {
                                detail: {
                                    id: statusId,
                                    label: statusId ? (options.label || '') : '',
                                },
                            }));
                        });
                    }
                },

                openClientStat(stat) {
                    if (!stat || !stat.clickable) {
                        return;
                    }

                    this.openFeature('list', {
                        processStatusId: stat.process_status_id || '',
                        label: stat.label || '',
                    }, true);
                },

                goHome(changeUrl) {
                    if (changeUrl === undefined) {
                        changeUrl = true;
                    }

                    this.screen = 'home';
                    this.moduleKey = '';
                    this.moduleTitle = '';
                    this.moduleDescription = '';
                    this.features = [];
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.sidebarOpen = false;
                    window.scrollTo(0, 0);
                    this.refreshModuleStats();

                    if (changeUrl) {
                        this.setUrl();
                    }
                },

                goModule() {
                    this.screen = 'module';
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.refreshModuleStats();
                    this.setUrl();
                },
            };
        }
    </script>
    @livewireScripts
</body>
</html>
