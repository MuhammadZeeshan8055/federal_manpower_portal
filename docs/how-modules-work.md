# How the dashboard screens work (simple)

Still one Laravel route: `/dashboard`.  
Alpine only changes which screen you see (like a small router).

## Three screens

| `screen` value | What you see |
|----------------|--------------|
| `home` | Module cards |
| `module` | Feature buttons for one module |
| `feature` | One feature page (Livewire screens where built) |

## Main variables (in `layouts/portal.blade.php`)

| Variable | Meaning |
|----------|---------|
| `screen` | `home` / `module` / `feature` |
| `moduleKey` | e.g. `clients` |
| `moduleTitle` | e.g. Clients |
| `features` | List of buttons for that module |
| `featureKey` | e.g. `list` |
| `featureTitle` | e.g. All Clients |

## Click trip (Clients)

1. Card: `@click="openModule('clients')"`
2. `openModule` sets `screen = 'module'` and fills `moduleTitle`, `features`, etc.
3. Feature button: `@click="openFeature('list')"`
4. `openFeature` sets `screen = 'feature'`
5. Clients are wired: `create` → bio form, `list` → All Clients (view/edit inside list). Operations masters are also Livewire on this screen.

## Functions you can call

| Function | Goes to |
|----------|---------|
| `openModule('clients')` | Module screen |
| `openFeature('list')` | Feature screen |
| `goModule()` | Back to feature buttons |
| `goHome()` | Back to overview cards |

## Files

- `config/portal.php` — module list
- `app/Support/PortalModules.php` — permission filter (plain foreach)
- `app/Http/Controllers/DashboardController.php` — loads data
- `resources/views/layouts/portal.blade.php` — Alpine state
- `resources/views/dashboard.blade.php` — three `x-show` screens
- `resources/views/partials/sidebar.blade.php` — Overview + current module links
