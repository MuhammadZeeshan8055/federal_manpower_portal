<?php

namespace App\Livewire\Employers;

use App\Models\EmployerProfile;
use App\Models\JobPost;
use App\Support\ClientPrivateFiles;
use App\Support\JobPosters;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageJobs extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected string $paginationTheme = 'portal';

    public bool $denied = false;

    public bool $canManage = false;

    public ?int $editingId = null;

    public string $employer_profile_id = '';

    public string $title = '';

    public string $category = '';

    public string $country = '';

    public string $city = '';

    public string $employment_type = 'Full time';

    public string $experience = '';

    public string $salary_text = '';

    public string $vacancies = '1';

    public string $description = '';

    public ?string $poster_path = null;

    public $poster = null;

    public string $search = '';

    public string $filter = 'all';

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || (! $user->canView('employers', 'jobs') && ! $user->canManage('employers', 'jobs'))) {
            $this->denied = true;

            return;
        }

        $this->canManage = $user->canManage('employers', 'jobs');
    }

    public function updatedSearch(): void
    {
        $this->resetPage('officeJobsPage');
    }

    public function updatedFilter(): void
    {
        $this->resetPage('officeJobsPage');
    }

    public function updatedPoster($value): void
    {
        if (! $value instanceof TemporaryUploadedFile) {
            return;
        }

        $this->poster = ClientPrivateFiles::compressTemporaryIfNeeded($value, true);
        $this->resetValidation('poster');
    }

    public function editJob(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $job = JobPost::query()->find($id);

        if (! $job) {
            return;
        }

        $this->editingId = $job->id;
        $this->employer_profile_id = $job->employer_profile_id ? (string) $job->employer_profile_id : '';
        $this->title = $job->title;
        $this->category = (string) ($job->category ?? '');
        $this->country = (string) ($job->country ?? '');
        $this->city = (string) ($job->city ?? '');
        $this->employment_type = (string) ($job->employment_type ?: 'Full time');
        $this->experience = (string) ($job->experience ?? '');
        $this->salary_text = (string) ($job->salary_text ?? '');
        $this->vacancies = (string) $job->vacancies;
        $this->description = (string) ($job->description ?? '');
        $this->poster_path = $job->poster_path;
        $this->poster = null;
        $this->successMessage = '';
        $this->resetValidation();
    }

    public function cancelEdit(): void
    {
        $this->successMessage = '';
        $this->resetForm();
        $this->dispatch('master-form-reset');
    }

    public function removePoster(): void
    {
        if ($this->denied || ! $this->canManage || ! $this->editingId) {
            return;
        }

        $job = JobPost::query()->find($this->editingId);

        if (! $job) {
            return;
        }

        JobPosters::deleteFolder($job->id);
        $job->update(['poster_path' => null]);
        $this->poster_path = null;
        $this->poster = null;
    }

    public function closeJob(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $job = JobPost::query()->find($id);

        if (! $job) {
            return;
        }

        $job->update(['status' => 'closed']);
        $this->flashToast('Job closed.', 'danger');
        $this->dispatch('refresh-module-stats');
    }

    public function reopenJob(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $job = JobPost::query()->find($id);

        if (! $job) {
            return;
        }

        $job->update([
            'status' => 'approved',
            'rejection_reason' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->flashToast('Job reopened and live on the website.', 'success');
        $this->dispatch('refresh-module-stats');
    }

    public function deleteJob(int $id): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $job = JobPost::query()->find($id);

        if (! $job) {
            return;
        }

        if ($this->editingId === $job->id) {
            $this->resetForm();
            $this->dispatch('master-form-reset');
        }

        JobPosters::deleteFolder($job->id);
        $job->delete();

        $this->flashToast('Job deleted.', 'danger');
        $this->resetPage('officeJobsPage');
        $this->dispatch('refresh-module-stats');
    }

    public function saveJob(): void
    {
        if ($this->denied || ! $this->canManage) {
            return;
        }

        $uploadMaxKb = JobPosters::uploadMaxKilobytes();

        $this->validate([
            'employer_profile_id' => ['nullable', 'integer', 'exists:employer_profiles,id'],
            'title' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:60'],
            'experience' => ['nullable', 'string', 'max:80'],
            'salary_text' => ['nullable', 'string', 'max:120'],
            'vacancies' => ['required', 'integer', 'min:1', 'max:9999'],
            'description' => ['nullable', 'string', 'max:5000'],
            'poster' => ['nullable', 'image', 'max:'.$uploadMaxKb, 'mimes:jpg,jpeg,png,webp'],
        ], [], [
            'employer_profile_id' => 'employer',
        ]);

        $employerId = $this->employer_profile_id !== '' ? (int) $this->employer_profile_id : null;

        $data = [
            'employer_profile_id' => $employerId,
            'title' => trim($this->title),
            'category' => trim($this->category) !== '' ? trim($this->category) : null,
            'country' => trim($this->country) !== '' ? trim($this->country) : null,
            'city' => trim($this->city) !== '' ? trim($this->city) : null,
            'employment_type' => trim($this->employment_type) !== '' ? trim($this->employment_type) : null,
            'experience' => trim($this->experience) !== '' ? trim($this->experience) : null,
            'salary_text' => trim($this->salary_text) !== '' ? trim($this->salary_text) : null,
            'vacancies' => (int) $this->vacancies,
            'description' => trim($this->description) !== '' ? trim($this->description) : null,
            'status' => 'approved',
            'rejection_reason' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ];

        if ($this->editingId) {
            $job = JobPost::query()->find($this->editingId);

            if (! $job) {
                return;
            }

            $job->update($data);

            if ($this->poster) {
                $path = JobPosters::store($job->id, $this->poster);
                $job->update(['poster_path' => $path]);
            }

            $this->flashToast('Job saved and live on the website.', 'success');
        } else {
            $job = JobPost::query()->create($data);

            if ($this->poster) {
                $path = JobPosters::store($job->id, $this->poster);
                $job->update(['poster_path' => $path]);
            }

            $this->flashToast('Job posted and live on the website.', 'success');
        }

        $this->resetForm();
        $this->resetPage('officeJobsPage');
        $this->dispatch('master-form-reset');
        $this->dispatch('refresh-module-stats');
    }

    public function render()
    {
        $jobs = null;
        $employers = collect();

        if (! $this->denied) {
            $query = JobPost::query()
                ->with('employer')
                ->orderByDesc('id');

            if ($this->filter !== 'all') {
                $query->where('status', $this->filter);
            }

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', $term)
                        ->orWhere('country', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhereHas('employer', function ($employerQuery) use ($term) {
                            $employerQuery->where('company_name', 'like', $term);
                        });
                });
            }

            $jobs = $query->paginate(12, ['*'], 'officeJobsPage');

            $employers = EmployerProfile::query()
                ->where('status', 'approved')
                ->orderBy('company_name')
                ->get(['id', 'company_name']);
        }

        return view('livewire.employers.manage-jobs', [
            'jobs' => $jobs,
            'employers' => $employers,
            'maxPosterMb' => max(1, (int) ceil(JobPosters::uploadMaxKilobytes() / 1024)),
            'currentPosterUrl' => JobPosters::url($this->poster_path),
        ]);
    }

    private function flashToast(string $message, string $type): void
    {
        $this->successMessage = $message;
        $this->toastType = $type;
        $this->toastVersion++;
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->employer_profile_id = '';
        $this->title = '';
        $this->category = '';
        $this->country = '';
        $this->city = '';
        $this->employment_type = 'Full time';
        $this->experience = '';
        $this->salary_text = '';
        $this->vacancies = '1';
        $this->description = '';
        $this->poster_path = null;
        $this->poster = null;
        $this->resetValidation();
    }
}
