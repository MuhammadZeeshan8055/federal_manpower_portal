<script>
    (function () {
        var savedTheme;
        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        try {
            savedTheme = localStorage.getItem('federal_theme');
        } catch (error) {
            savedTheme = null;
        }

        var theme = savedTheme === 'dark' || savedTheme === 'light'
            ? savedTheme
            : (prefersDark ? 'dark' : 'light');

        document.documentElement.dataset.theme = theme;
        document.documentElement.style.colorScheme = theme;

        var themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.setAttribute('content', theme === 'dark' ? '#0b1520' : '#145f90');
        }
    })();
</script>
