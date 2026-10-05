<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#145f90">
    <title>@yield('title', 'Dashboard') — Federal Manpower Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="portal-body" x-data="portalApp()" :class="{ 'nav-open': sidebarOpen }">
    <div class="portal-shell">
        @include('partials.sidebar')

        <div class="portal-main">
            @include('partials.header')
            <main class="portal-content">
                @yield('content')
            </main>
        </div>
    </div>

    <button class="nav-overlay" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" aria-label="Close navigation"></button>

    <script>
        function portalApp() {
            return {
                sidebarOpen: false,
                profileOpen: false,
                activeSection: 'overview',
                sectionTitles: {
                    overview: ['Overview', 'A clear view of your manpower operations.'],
                    recruitment: ['Recruitment', 'Manage open roles and hiring activity.'],
                    candidates: ['Candidates', 'Review and manage candidate profiles.'],
                    workforce: ['Workforce', 'Manage deployed and available personnel.'],
                    clients: ['Clients', 'Track partner companies and requirements.'],
                    documents: ['Documents', 'Monitor document and visa processing.'],
                    reports: ['Reports', 'Explore operational performance.'],
                    settings: ['Settings', 'Configure your organization workspace.']
                },
                setSection(section) {
                    this.activeSection = section;
                    this.sidebarOpen = false;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    </script>
</body>
</html>
