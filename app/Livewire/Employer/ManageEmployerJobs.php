<?php

namespace App\Livewire\Employer;

use App\Models\JobPost;
use App\Support\EmployerNotifications;
use App\Support\JobPosters;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageEmployerJobs extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $denied = false;

    public bool $notApproved = false;

    public ?int $editingId = null;

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

    public string $successMessage = '';

    public int $toastVersion = 0;

    public string $toastType = 'success';

    public function mount(): void
    {
        $user = auth()->user();
        $profile = $user?->employerProfile;

        if (! $user || ! $user->isEmployer() || ! $profile) {
            $this->denied = true;

            return;
        }

        if (! $profile->isApproved()) {
            $this->notApproved = true;
        }
    }

    public function updatedPoster($value): void
    {
        if (! $value instanceof TemporaryUploadedFile) {
            return;
        }

        $this->resetValidation('poster');
    }

    public function editJob(int $id): void
    {
        if ($this->denied || $this->notApproved) {
            return;
        }

        $job = $this->findOwnJob($id);

        if (! $job) {
            return;
        }

        $this->editingId = $job->id;
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
        $this->resetForm();
    }

    public function removePoster(): void
    {
        if ($this->denied || $this->notApproved || ! $this->editingId) {
            return;
        }

        $job = $this->findOwnJob($this->editingId);

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
        if ($this->denied || $this->notApproved) {
            return;
        }

        $job = $this->findOwnJob($id);

        if (! $job) {
            return;
        }

        $job->update([
            'status' => 'closed',
        ]);

        $this->flashToast('Job closed.', 'danger');
    }

    public function deleteJob(int $id): void
    {
        if ($this->denied || $this->notApproved) {
            return;
        }

        $job = $this->findOwnJob($id);

        if (! $job) {
            return;
        }

        if ($this->editingId === $job->id) {
            $this->resetForm();
        }

        JobPosters::deleteFolder($job->id);
        $job->delete();

        $this->flashToast('Job deleted.', 'danger');
        $this->resetPage('employerJobsPage');
    }

    public function saveJob(): void
    {
        if ($this->denied || $this->notApproved) {
            return;
        }

        $maxKb = JobPosters::maxKilobytes();

        $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:80'],
            'country' => ['nullable', 'string', 'max:120'],
            'city' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:60'],
            'experience' => ['nullable', 'string', 'max:80'],
            'salary_text' => ['nullable', 'string', 'max:120'],
            'vacancies' => ['required', 'integer', 'min:1', 'max:9999'],
            'description' => ['nullable', 'string', 'max:5000'],
            'poster' => ['nullable', 'image', 'max:'.$maxKb, 'mimes:jpg,jpeg,png,webp'],
        ]);

        $profile = auth()->user()->employerProfile;

        $data = [
            'title' => trim($this->title),
            'category' => trim($this->category) !== '' ? trim($this->category) : null,
            'country' => trim($this->country) !== '' ? trim($this->country) : null,
            'city' => trim($this->city) !== '' ? trim($this->city) : null,
            'employment_type' => trim($this->employment_type) !== '' ? trim($this->employment_type) : null,
            'experience' => trim($this->experience) !== '' ? trim($this->experience) : null,
            'salary_text' => trim($this->salary_text) !== '' ? trim($this->salary_text) : null,
            'vacancies' => (int) $this->vacancies,
            'description' => trim($this->description) !== '' ? trim($this->description) : null,
            'status' => 'pending',
            'rejection_reason' => null,
            'approved_by' => null,
            'approved_at' => null,
        ];

        if ($this->editingId) {
            $job = $this->findOwnJob($this->editingId);

            if (! $job) {
                return;
            }

            $job->update($data);

            if ($this->poster) {
                $path = JobPosters::store($job->id, $this->poster);
                $job->update(['poster_path' => $path]);
            }

            EmployerNotifications::jobSubmitted($job->fresh(), true);
            $this->flashToast('Job updated — waiting for admin approval again.', 'success');
        } else {
            $data['employer_profile_id'] = $profile->id;
            $job = JobPost::query()->create($data);

            if ($this->poster) {
                $path = JobPosters::store($job->id, $this->poster);
                $job->update(['poster_path' => $path]);
            }

            EmployerNotifications::jobSubmitted($job->fresh());
            $this->flashToast('Job submitted — waiting for admin approval.', 'success');
        }

        $this->resetForm();
        $this->resetPage('employerJobsPage');
    }

    public function render()
    {
        $jobs = null;

        if (! $this->denied && ! $this->notApproved && auth()->user()?->employerProfile) {
            $jobs = JobPost::query()
                ->where('employer_profile_id', auth()->user()->employerProfile->id)
                ->orderByDesc('id')
                ->paginate(10, ['*'], 'employerJobsPage');
        }

        return view('livewire.employer.manage-employer-jobs', [
            'jobs' => $jobs,
            'maxPosterMb' => max(1, (int) ceil(JobPosters::maxKilobytes() / 1024)),
            'currentPosterUrl' => JobPosters::url($this->poster_path),
        ])->layout('layouts.employer-portal', [
            'title' => 'My jobs',
        ]);
    }

    private function findOwnJob(int $id): ?JobPost
    {
        $profileId = auth()->user()?->employerProfile?->id;

        if (! $profileId) {
            return null;
        }

        return JobPost::query()
            ->whereKey($id)
            ->where('employer_profile_id', $profileId)
            ->first();
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
