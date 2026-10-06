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
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    @livewireStyles
</head>
<body
    class="portal-body"
    x-data="portalApp()"
    :class="{ 'nav-open': sidebarOpen }"
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
         * Simple page state (like a tiny router, still on /dashboard):
         *   screen = 'home'     → overview cards
         *   screen = 'module'   → one module open (feature buttons)
         *   screen = 'feature'  → one feature open (table/form later)
         */
        function portalApp() {
            return {
                sidebarOpen: false,
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

                init() {
                    this.applyTheme(this.darkMode ? 'dark' : 'light', false);
                    this.loadSavedScreen();
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

                openModule(key) {
                    var module = this.modules[key];
                    if (!module) {
                        return;
                    }

                    this.screen = 'module';
                    this.moduleKey = key;
                    this.moduleTitle = module.title;
                    this.moduleDescription = module.description;
                    this.features = module.children ? module.children : [];
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.sidebarOpen = false;
                    this.saveScreen();
                    window.scrollTo(0, 0);
                },

                openFeature(key) {
                    var i;
                    var feature = null;

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
                    this.saveScreen();
                    window.scrollTo(0, 0);
                },

                goHome() {
                    this.screen = 'home';
                    this.moduleKey = '';
                    this.moduleTitle = '';
                    this.moduleDescription = '';
                    this.features = [];
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.sidebarOpen = false;
                    this.saveScreen();
                    window.scrollTo(0, 0);
                },

                goModule() {
                    this.screen = 'module';
                    this.featureKey = '';
                    this.featureTitle = '';
                    this.saveScreen();
                },

                saveScreen() {
                    localStorage.setItem('federal_screen', JSON.stringify({
                        screen: this.screen,
                        moduleKey: this.moduleKey,
                        featureKey: this.featureKey,
                    }));
                },

                loadSavedScreen() {
                    var saved = localStorage.getItem('federal_screen');
                    var data;

                    if (!saved) {
                        return;
                    }

                    try {
                        data = JSON.parse(saved);
                    } catch (e) {
                        localStorage.removeItem('federal_screen');
                        return;
                    }

                    if (!data.moduleKey || !this.modules[data.moduleKey]) {
                        return;
                    }

                    this.openModule(data.moduleKey);

                    if (data.featureKey) {
                        this.openFeature(data.featureKey);
                    }
                },
            };
        }
    </script>
    @livewireScripts
</body>
</html>
