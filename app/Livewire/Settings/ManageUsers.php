<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Models\UserPermission;
use App\Support\GrantableModules;
use App\Support\PortalRefresh;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ManageUsers extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $formOpen = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'staff';

    public bool $bypass_attendance = false;

    public string $monthly_salary = '';

    public bool $allowAll = false;

    // Example: ['clients' => ['on' => true, 'access' => 'manage']]
    public array $moduleAccess = [];

    public string $search = '';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        if (! auth()->user()?->canManageUsers()) {
            $this->denied = true;

            return;
        }

        $this->clearModuleAccess();
    }

    public function updatedSearch(): void
    {
        $this->resetPage('usersPage');
    }

    // When "Allow all" is ticked → turn every module on as Manage
    public function updatedAllowAll($value): void
    {
        if (! $value) {
            return;
        }

        foreach (GrantableModules::list() as $module) {
            $this->moduleAccess[$module['key']] = [
                'on' => true,
                'access' => 'manage',
            ];
        }
    }

    public function openCreate(): void
    {
        if ($this->denied) {
            return;
        }

        $this->clearForm();
        $this->formOpen = true;
    }

    public function editUser(int $id): void
    {
        if ($this->denied) {
            return;
        }

        $user = $this->findOfficeUser($id);

        if (! $user) {
            return;
        }

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->role = $user->role;
        $this->bypass_attendance = (bool) $user->bypass_attendance;
        $this->monthly_salary = $user->monthly_salary !== null ? (string) $user->monthly_salary : '';
        $this->fillModuleAccessFromUser($user);
        $this->formOpen = true;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelForm(): void
    {
        $this->formOpen = false;
        $this->clearForm();
    }

    public function saveUser(): void
    {
        if ($this->denied) {
            return;
        }

        $this->validate($this->rules());

        if ($this->editingId) {
            $user = $this->updateExistingUser();
        } else {
            $user = $this->createNewUser();
        }

        if (! $user) {
            return;
        }

        // Admin roles: clear permission rows (they already see everything)
        if ($this->isAdminRole($this->role)) {
            UserPermission::query()->where('user_id', $user->id)->delete();
        } else {
            $this->writePermissions($user);
        }

        $this->showToast('success', $this->editingId ? 'User updated.' : 'User created.');
        $this->formOpen = false;
        $this->clearForm();
        $this->resetPage('usersPage');
    }

    public function deleteUser(int $id): void
    {
        if ($this->denied) {
            return;
        }

        if ($id === auth()->id()) {
            $this->showToast('danger', 'You cannot delete your own account.');

            return;
        }

        $user = $this->findOfficeUser($id);

        if (! $user) {
            return;
        }

        UserPermission::query()->where('user_id', $user->id)->delete();
        $user->delete();

        if ($this->editingId === $id) {
            $this->cancelForm();
        }

        $this->showToast('danger', 'User deleted.');
        $this->resetPage('usersPage');
    }

    public function render()
    {
        return view('livewire.settings.manage-users', [
            'users' => $this->usersList(),
            'grantModules' => GrantableModules::list(),
            'roleOptions' => $this->roleOptions(),
            'showPermissionGrid' => ! $this->isAdminRole($this->role),
        ]);
    }

    // --- helpers below ---

    private function rules(): array
    {
        $roles = ['admin', 'hr', 'accounts', 'staff'];

        if (auth()->user()->isSuperAdmin()) {
            $roles[] = 'super_admin';
        }

        $passwordRule = $this->editingId
            ? ['nullable', 'string', 'min:8', 'max:100']
            : ['required', 'string', 'min:8', 'max:100'];

        return [
            'name' => ['required', 'string', 'max:160'],
            'email' => [
                'required',
                'email',
                'max:160',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'role' => ['required', Rule::in($roles)],
            'password' => $passwordRule,
            'bypass_attendance' => ['boolean'],
            'monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'allowAll' => ['boolean'],
            'moduleAccess' => ['array'],
        ];
    }

    private function createNewUser(): User
    {
        return User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'bypass_attendance' => $this->wfhValue(),
            'monthly_salary' => $this->salaryValue(),
            'email_verified_at' => now(),
        ]);
    }

    private function updateExistingUser(): ?User
    {
        $user = $this->findOfficeUser($this->editingId);

        if (! $user) {
            return null;
        }

        // Block removing your own admin role
        if ($user->id === auth()->id() && $user->isAdmin() && ! $this->isAdminRole($this->role)) {
            $this->addError('role', 'You cannot remove your own admin role.');

            return null;
        }

        $user->name = $this->name;
        $user->email = $this->email;
        $user->role = $this->role;
        $user->bypass_attendance = $this->wfhValue();
        $user->monthly_salary = $this->salaryValue();

        if ($this->password !== '') {
            $user->password = Hash::make($this->password);
        }

        $user->save();

        return $user;
    }

    // Admins always skip gate — WFH flag only for other roles
    private function wfhValue(): bool
    {
        if ($this->isAdminRole($this->role)) {
            return false;
        }

        return (bool) $this->bypass_attendance;
    }

    private function salaryValue(): ?float
    {
        if (trim($this->monthly_salary) === '') {
            return null;
        }

        return (float) $this->monthly_salary;
    }

    private function writePermissions(User $user): void
    {
        UserPermission::query()->where('user_id', $user->id)->delete();

        foreach ($this->moduleAccess as $moduleKey => $row) {
            if (empty($row['on'])) {
                continue;
            }

            $access = ($row['access'] ?? '') === 'view' ? 'view' : 'manage';
            $features = GrantableModules::featureKeysFor($moduleKey, $user->role);

            foreach ($features as $featureKey) {
                UserPermission::query()->create([
                    'user_id' => $user->id,
                    'module_key' => $moduleKey,
                    'feature_key' => $featureKey,
                    'access' => $access,
                ]);
            }
        }
    }

    private function fillModuleAccessFromUser(User $user): void
    {
        $this->clearModuleAccess();
        $this->allowAll = false;

        if ($user->isAdmin()) {
            $this->allowAll = true;
            $this->updatedAllowAll(true);

            return;
        }

        $rows = UserPermission::query()
            ->where('user_id', $user->id)
            ->get();

        // Collect access per module
        $accessByModule = [];

        foreach ($rows as $row) {
            $mod = $row->module_key;

            if (! isset($accessByModule[$mod])) {
                $accessByModule[$mod] = 'view';
            }

            if ($row->access === 'manage') {
                $accessByModule[$mod] = 'manage';
            }
        }

        $everyModuleIsManage = true;

        foreach (GrantableModules::list() as $module) {
            $key = $module['key'];

            if (! isset($accessByModule[$key])) {
                $everyModuleIsManage = false;
                continue;
            }

            $this->moduleAccess[$key] = [
                'on' => true,
                'access' => $accessByModule[$key],
            ];

            if ($accessByModule[$key] !== 'manage') {
                $everyModuleIsManage = false;
            }
        }

        $this->allowAll = $everyModuleIsManage && count($accessByModule) === count(GrantableModules::list());
    }

    private function usersList()
    {
        if ($this->denied) {
            return collect();
        }

        $query = User::query()
            ->where('role', '!=', 'client')
            ->orderBy('name');

        $search = trim($this->search);

        if ($search !== '') {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('role', 'like', $like);
            });
        }

        return $query->paginate(12, pageName: 'usersPage');
    }

    private function roleOptions(): array
    {
        $options = [
            'staff' => 'Staff',
            'hr' => 'HR',
            'accounts' => 'Accounts',
            'admin' => 'Admin',
        ];

        if (auth()->user()->isSuperAdmin()) {
            $options = ['super_admin' => 'Super Admin'] + $options;
        }

        return $options;
    }

    private function findOfficeUser(?int $id): ?User
    {
        if (! $id) {
            return null;
        }

        return User::query()
            ->where('role', '!=', 'client')
            ->find($id);
    }

    private function isAdminRole(string $role): bool
    {
        return $role === 'admin' || $role === 'super_admin';
    }

    private function clearForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'staff';
        $this->bypass_attendance = false;
        $this->monthly_salary = '';
        $this->allowAll = false;
        $this->successMessage = '';
        $this->clearModuleAccess();
        $this->resetValidation();
    }

    private function clearModuleAccess(): void
    {
        $this->moduleAccess = [];

        foreach (GrantableModules::list() as $module) {
            $this->moduleAccess[$module['key']] = [
                'on' => false,
                'access' => 'manage',
            ];
        }
    }

    private function showToast(string $type, string $message): void
    {
        $this->toastType = $type;
        $this->successMessage = $message;
        $this->toastVersion++;
        $this->js(PortalRefresh::jsNotifyListsChanged());
    }
}
