<div>
    @if ($denied)
        <section class="page-hero page-hero--employers">
            <div class="site-wrap page-hero__inner">
                <span class="section-kicker">Employer portal</span>
                <h1>No access.</h1>
                <p>An employer account is required for this page.</p>
            </div>
        </section>
    @elseif ($notApproved)
        <section class="page-hero page-hero--employers">
            <div class="site-wrap page-hero__inner">
                <span class="section-kicker">My jobs</span>
                <h1>Jobs locked.</h1>
                <p>Your company is not approved yet. You can post jobs after admin approval.</p>
            </div>
        </section>
        <section class="page-section page-section--soft">
            <div class="site-wrap">
                <a class="btn btn-ghost" href="{{ route('employer.home') }}">Back to dashboard</a>
            </div>
        </section>
    @else
        <section class="page-hero page-hero--employers">
            <div class="site-wrap page-hero__inner">
                <span class="section-kicker">My jobs</span>
                <h1>{{ $editingId ? 'Edit job.' : 'Post a job.' }}</h1>
                <p>New and edited jobs stay pending until an admin approves them for the public site.</p>
            </div>
        </section>

        <section class="page-section page-section--soft">
            <div class="site-wrap ep-jobs">
                @if ($successMessage !== '')
                    <div
                        class="ep-toast ep-toast--{{ $toastType }}"
                        wire:key="employer-job-toast-{{ $toastVersion }}"
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-show="show"
                        role="status"
                    >
                        {{ $successMessage }}
                    </div>
                @endif

                <div class="ep-jobs__grid">
                    <form class="contact-form" wire:submit="saveJob">
                        <h2>{{ $editingId ? 'Edit vacancy' : 'New vacancy' }}</h2>

                        <label>
                            Job title
                            <input type="text" wire:model="title" maxlength="160" placeholder="e.g. Construction Worker">
                            @error('title') <span class="ep-field-error">{{ $message }}</span> @enderror
                        </label>

                        <div class="form-row">
                            <label>
                                Category
                                <input type="text" wire:model="category" maxlength="80" placeholder="e.g. Construction">
                                @error('category') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Employment type
                                <select wire:model="employment_type">
                                    <option value="Full time">Full time</option>
                                    <option value="Contract">Contract</option>
                                    <option value="Part time">Part time</option>
                                </select>
                                @error('employment_type') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="form-row">
                            <label>
                                Country
                                <input type="text" wire:model="country" maxlength="120" placeholder="e.g. UAE">
                                @error('country') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                City
                                <input type="text" wire:model="city" maxlength="120">
                                @error('city') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <div class="form-row">
                            <label>
                                Experience
                                <input type="text" wire:model="experience" maxlength="80" placeholder="e.g. 2+ years">
                                @error('experience') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Salary
                                <input type="text" wire:model="salary_text" maxlength="120" placeholder="e.g. $500 - $700 / month">
                                @error('salary_text') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>

                        <label>
                            Vacancies
                            <input type="number" wire:model="vacancies" min="1" max="9999">
                            @error('vacancies') <span class="ep-field-error">{{ $message }}</span> @enderror
                        </label>

                        <label>
                            Description
                            <textarea wire:model="description" rows="4" maxlength="5000" placeholder="Role details, requirements..."></textarea>
                            @error('description') <span class="ep-field-error">{{ $message }}</span> @enderror
                        </label>

                        <div class="ep-actions">
                            <button type="submit" class="btn btn-accent">
                                {{ $editingId ? 'Save & submit for approval' : 'Submit for approval' }}
                                <span>&rarr;</span>
                            </button>
                            @if ($editingId)
                                <button type="button" class="btn btn-ghost" wire:click="cancelEdit">Cancel</button>
                            @endif
                        </div>
                    </form>

                    <div class="contact-form ep-jobs-list">
                        <h2>Your jobs</h2>
                        <div class="ep-table-wrap">
                            <table class="ep-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Country</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jobs as $job)
                                        <tr wire:key="employer-job-{{ $job->id }}">
                                            <td>{{ $job->title }}</td>
                                            <td>{{ $job->country ?: '—' }}</td>
                                            <td>
                                                <span class="ep-status ep-status--{{ $job->status }}">{{ $job->status }}</span>
                                                @if ($job->isRejected() && $job->rejection_reason)
                                                    <div class="ep-muted">{{ $job->rejection_reason }}</div>
                                                @endif
                                            </td>
                                            <td class="ep-row-actions">
                                                @if (! $job->isClosed())
                                                    <button type="button" class="ep-link" wire:click="editJob({{ $job->id }})">Edit</button>
                                                    <button type="button" class="ep-link ep-link--danger" wire:click="closeJob({{ $job->id }})" wire:confirm="Close this job?">Close</button>
                                                @endif
                                                <button type="button" class="ep-link ep-link--danger" wire:click="deleteJob({{ $job->id }})" wire:confirm="Delete this job permanently?">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="ep-muted">No jobs yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($jobs instanceof \Illuminate\Pagination\LengthAwarePaginator && $jobs->hasPages())
                            <div class="ep-pagination">
                                @if ($jobs->onFirstPage())
                                    <span class="ep-page is-disabled">Previous</span>
                                @else
                                    <button type="button" class="ep-page" wire:click="previousPage('employerJobsPage')">Previous</button>
                                @endif
                                <span class="ep-page-meta">Page {{ $jobs->currentPage() }} of {{ $jobs->lastPage() }}</span>
                                @if ($jobs->hasMorePages())
                                    <button type="button" class="ep-page" wire:click="nextPage('employerJobsPage')">Next</button>
                                @else
                                    <span class="ep-page is-disabled">Next</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
