<div
    class="master-page"
    x-data="{
        formOpen: @js((bool) $editingId),
        deleteOpen: false,
        deleteId: null,
        deleteName: '',
        deleteTrigger: null,
        openDelete(id, name, trigger) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteTrigger = trigger;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.deleteCancel.focus());
        },
        closeDelete() {
            this.deleteOpen = false;
            this.$nextTick(() => this.deleteTrigger?.focus());
        }
    }"
    @keydown.escape.window="if (deleteOpen) closeDelete()"
    @master-form-reset.window="formOpen = false"
>
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to manage jobs.</p>
        </div>
    @else
        @if ($canManage)
            <section class="panel master-form-panel master-form-panel--collapsible" :class="{ 'master-form-panel--open': formOpen }">
                @include('partials.master-form-toggle', [
                    'targetId' => 'office-job-form-fields',
                    'eyebrow' => $editingId ? 'EDIT' : 'ADD',
                    'title' => $editingId ? 'Edit job' : 'Post a job',
                    'actionLabel' => 'Post job',
                ])

                @if ($successMessage !== '')
                    <div
                        class="portal-toast portal-toast--{{ $toastType }}"
                        wire:key="office-job-toast-{{ $toastVersion }}"
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 5000)"
                        x-show="show"
                        role="status"
                    >
                        <span class="portal-toast__copy"><strong>{{ $successMessage }}</strong></span>
                    </div>
                @endif

                <div id="office-job-form-fields" class="master-form-collapse" :class="{ 'master-form-collapse--open': formOpen }" :aria-hidden="(!formOpen).toString()" :inert="!formOpen">
                    <div class="master-form-collapse__inner"><div class="master-form-content">
                        <form class="master-form" wire:submit="saveJob">
                            <p class="muted" style="margin:0 0 12px;">Goes live on the website immediately — no approval step.</p>

                            <label>
                                Employer company (optional)
                                @include('partials.searchable-select', [
                                    'options' => $employers->map(fn ($employer) => ['id' => $employer->id, 'name' => $employer->company_name]),
                                    'wireModel' => 'employer_profile_id',
                                    'selectedId' => $employer_profile_id,
                                    'placeholder' => 'Office post (no employer)',
                                    'searchPlaceholder' => 'Search employer...',
                                    'emptyText' => 'No approved employer found',
                                ])
                                @error('employer_profile_id') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Job title
                                <input type="text" wire:model="title" maxlength="160" placeholder="e.g. Construction Worker">
                                @error('title') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <div class="form-row">
                                <label>
                                    Category
                                    <input type="text" wire:model="category" maxlength="80" placeholder="e.g. Construction">
                                    @error('category') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    Employment type
                                    <select wire:model="employment_type">
                                        <option value="Full time">Full time</option>
                                        <option value="Contract">Contract</option>
                                        <option value="Part time">Part time</option>
                                    </select>
                                    @error('employment_type') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            <div class="form-row">
                                <label>
                                    Country
                                    <input type="text" wire:model="country" maxlength="120" placeholder="e.g. UAE">
                                    @error('country') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    City
                                    <input type="text" wire:model="city" maxlength="120">
                                    @error('city') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            <div class="form-row">
                                <label>
                                    Experience
                                    <input type="text" wire:model="experience" maxlength="80" placeholder="e.g. 2+ years">
                                    @error('experience') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    Salary
                                    <input type="text" wire:model="salary_text" maxlength="120" placeholder="e.g. $500 - $700 / month">
                                    @error('salary_text') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                            </div>

                            <label>
                                Vacancies
                                <input type="number" wire:model="vacancies" min="1" max="9999">
                                @error('vacancies') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Description
                                <textarea wire:model="description" rows="4" maxlength="5000" placeholder="Role details, requirements..."></textarea>
                                @error('description') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            <label>
                                Job poster image
                                <input type="file" wire:model="poster" accept="image/jpeg,image/png,image/webp,.jpg,.jpeg,.png,.webp">
                                <span class="muted" style="display:block;margin-top:6px;font-size:12px;">
                                    Optional HD poster (JPG / PNG / WebP · up to {{ $maxPosterMb }} MB). Auto-shrunk while staying clear.
                                </span>
                                @error('poster') <span class="master-error">{{ $message }}</span> @enderror
                            </label>

                            @if ($poster)
                                <div style="margin:8px 0 12px;">
                                    <img src="{{ $poster->temporaryUrl() }}" alt="New poster preview" style="max-width:220px;border-radius:10px;border:1px solid var(--line,#dce9ef);">
                                </div>
                            @elseif ($currentPosterUrl)
                                <div style="margin:8px 0 12px;">
                                    <img src="{{ $currentPosterUrl }}" alt="Current job poster" style="max-width:220px;border-radius:10px;border:1px solid var(--line,#dce9ef);">
                                    @if ($editingId)
                                        <div style="margin-top:6px;">
                                            <button type="button" class="row-action row-action--delete" wire:click="removePoster" wire:confirm="Remove this poster?">
                                                Remove poster
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="master-form__actions">
                                <button type="submit" class="button button--soft" wire:loading.attr="disabled">
                                    {{ $editingId ? 'Save & publish' : 'Post & publish' }}
                                </button>
                                @if ($editingId)
                                    <button type="button" class="button button--ghost" wire:click="cancelEdit" @click="formOpen = false">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div></div>
                </div>
            </section>
        @endif

        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">EMPLOYERS</p>
                    <h3>Jobs</h3>
                </div>
                <div class="visitors-toolbar">
                    <select class="visitors-toolbar__select" wire:model.live="filter" aria-label="Status">
                        <option value="all">All</option>
                        <option value="approved">Live</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                        <option value="closed">Closed</option>
                    </select>
                    <label class="master-search">
                        <span class="master-search__icon" aria-hidden="true">@include('partials.icon', ['name' => 'search'])</span>
                        <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search jobs..." aria-label="Search">
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table employers-approvals-table">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Company</th>
                            <th>Country</th>
                            <th>Status</th>
                            @if ($canManage)
                                <th class="master-actions-heading">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            <tr wire:key="office-job-{{ $job->id }}">
                                <td class="employers-approvals-table__text">
                                    <div style="display:flex;gap:10px;align-items:center;">
                                        @if ($job->hasPoster())
                                            <img
                                                src="{{ $job->posterUrl() }}"
                                                alt=""
                                                style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid var(--line,#dce9ef);flex-shrink:0;"
                                            >
                                        @endif
                                        <div>
                                            <strong>{{ $job->title }}</strong>
                                            <div class="muted">{{ $job->category ?: '—' }} · {{ $job->employment_type ?: '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="employers-approvals-table__text">{{ $job->employer?->company_name ?? 'Office' }}</td>
                                <td>{{ $job->country ?: '—' }}</td>
                                <td>
                                    @if ($job->isPending())
                                        <span class="status status--interview">Pending</span>
                                    @elseif ($job->isApproved())
                                        <span class="status status--ready">Live</span>
                                    @elseif ($job->isClosed())
                                        <span class="status status--interview">Closed</span>
                                    @else
                                        <span class="status status--hold">{{ ucfirst($job->status) }}</span>
                                    @endif
                                </td>
                                @if ($canManage)
                                    <td class="master-row-actions">
                                        <button type="button" class="row-action row-action--edit" wire:click="editJob({{ $job->id }})" @click="formOpen = true">
                                            @include('partials.icon', ['name' => 'pencil'])
                                            <span>Edit</span>
                                        </button>
                                        @if ($job->isClosed())
                                            <button type="button" class="row-action row-action--edit" wire:click="reopenJob({{ $job->id }})" wire:confirm="Reopen and publish this job?">
                                                <span>Reopen</span>
                                            </button>
                                        @else
                                            <button type="button" class="row-action row-action--delete" wire:click="closeJob({{ $job->id }})" wire:confirm="Close this job?">
                                                <span>Close</span>
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            class="row-action row-action--delete"
                                            @click="openDelete({{ $job->id }}, @js($job->title), $el)"
                                        >
                                            @include('partials.icon', ['name' => 'trash'])
                                            <span>Delete</span>
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canManage ? 5 : 4 }}" class="muted">No jobs yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($jobs)
                {{ $jobs->links() }}
            @endif
        </section>

        @if ($canManage)
            <template x-teleport="body">
                <div
                    class="confirm-backdrop"
                    x-show="deleteOpen"
                    x-cloak
                    @click.self="closeDelete()"
                    role="presentation"
                >
                    <section class="confirm-modal" role="alertdialog" aria-modal="true">
                        <button type="button" class="confirm-modal__close" @click="closeDelete()" aria-label="Close dialog">&times;</button>
                        <p class="confirm-modal__eyebrow">DELETE</p>
                        <h3>Delete this job?</h3>
                        <p>
                            <strong x-text="deleteName"></strong>
                            will be removed permanently.
                        </p>
                        <div class="confirm-modal__actions">
                            <button type="button" class="confirm-button confirm-button--cancel" x-ref="deleteCancel" @click="closeDelete()">Cancel</button>
                            <button
                                type="button"
                                class="confirm-button confirm-button--delete"
                                @click="const id = deleteId; closeDelete(); $wire.deleteJob(id)"
                            >
                                Delete
                            </button>
                        </div>
                    </section>
                </div>
            </template>
        @endif
    @endif
</div>
