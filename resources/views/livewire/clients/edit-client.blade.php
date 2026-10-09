<div class="bio-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to edit clients.</p>
        </div>
    @elseif ($notFound)
        <div class="panel empty-card">
            <h3>Client not found</h3>
            <button type="button" class="button button--soft" wire:click="backToList">Back to list</button>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="edit-client-toast-{{ $toastVersion }}"
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="toast-enter"
                x-transition:enter-start="toast-enter-start"
                x-transition:enter-end="toast-enter-end"
                x-transition:leave="toast-leave"
                x-transition:leave-start="toast-leave-start"
                x-transition:leave-end="toast-leave-end"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            >
                <span class="portal-toast__icon">
                    @include('partials.icon', ['name' => 'check'])
                </span>
                <span class="portal-toast__copy">
                    <small>Success</small>
                    <strong>{{ $successMessage }}</strong>
                </span>
                <button type="button" class="portal-toast__close" @click="show = false" aria-label="Dismiss notification">
                    &times;
                </button>
                <span class="portal-toast__progress" aria-hidden="true"></span>
            </div>
        @endif

        <section class="panel bio-panel">
            <div class="bio-toolbar bio-toolbar--simple">
                <button type="button" class="text-button back-button" wire:click="backToList">
                    <span aria-hidden="true">&larr;</span> Back to list
                </button>
            </div>

            <div class="bio-toolbar">
                <div class="bio-toolbar__job">
                    <label>
                        Job title
                        @include('partials.searchable-select', [
                            'options' => $trades,
                            'wireModel' => 'trade_id',
                            'selectedId' => $trade_id,
                            'placeholder' => 'Select trade',
                            'searchPlaceholder' => 'Search trade...',
                            'emptyText' => 'No trade found',
                        ])
                    </label>
                    <label>
                        Country
                        @include('partials.searchable-select', [
                            'options' => $countries,
                            'wireModel' => 'country_id',
                            'selectedId' => $country_id,
                            'placeholder' => 'Select country',
                            'searchPlaceholder' => 'Search country...',
                            'emptyText' => 'No country found',
                        ])
                    </label>
                </div>
            </div>

            <form class="bio-form" wire:submit="saveClient">
                <div class="bio-collapse" x-data="{ open: true }" :class="{ 'is-open': open }">
                    <button type="button" class="bio-collapse__toggle" @click="open = !open" :aria-expanded="open.toString()">
                        <span>Personal details</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="bio-collapse__body" x-show="open">
                        <div class="bio-form__grid">
                            <label class="bio-form__full">
                                <span class="bio-form__label">Name <span class="bio-required">*</span></span>
                                <input type="text" wire:model="full_name" required>
                                @error('full_name') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>Height <input type="text" wire:model="height"></label>
                            <label>Weight <input type="text" wire:model="weight"></label>
                            <label>
                                <span class="bio-form__label">Father's name <span class="bio-required">*</span></span>
                                <input type="text" wire:model="father_name" required>
                                @error('father_name') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Gender <span class="bio-required">*</span></span>
                                <select wire:model="gender" required>
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Mother's name <span class="bio-required">*</span></span>
                                <input type="text" wire:model="mother_name" required>
                                @error('mother_name') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Date of birth <span class="bio-required">*</span></span>
                                <input type="date" wire:model.live="date_of_birth" required>
                                @error('date_of_birth') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>Age <input type="text" value="{{ $age }}" readonly tabindex="-1"></label>
                            <label>
                                <span class="bio-form__label">Marital status <span class="bio-required">*</span></span>
                                <select wire:model="marital_status" required>
                                    <option value="">Select</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                                @error('marital_status') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Religion <span class="bio-required">*</span></span>
                                <input type="text" wire:model="religion" required>
                                @error('religion') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">ID number (CNIC) <span class="bio-required">*</span></span>
                                <input type="text" wire:model="cnic" required>
                                @error('cnic') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>Place of birth <input type="text" wire:model="place_of_birth"></label>
                            <label>Phone <input type="tel" wire:model="phone"></label>
                            <label>Email <input type="email" wire:model="email"></label>
                            <label class="bio-form__full">Permanent address <input type="text" wire:model="address"></label>
                            <label>Police station <input type="text" wire:model="police_station"></label>
                            <label>District <input type="text" wire:model="district"></label>
                            <label>
                                Medical fitness
                                <select wire:model="medical_fitness">
                                    <option value="">Select</option>
                                    <option value="fit">Fit</option>
                                    <option value="unfit">Unfit</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </label>
                            <label class="bio-form__full">
                                Photo
                                @if ($photo_path)
                                    <a class="bio-doc-link" href="{{ route('clients.photo.show', $clientId) }}" target="_blank" rel="noopener">
                                        View current photo
                                    </a>
                                @endif
                                <span class="bio-file-placeholder">
                                    <input type="file" wire:model="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                    <small>Replace · JPG/PNG · max {{ $maxUploadMb }} MB</small>
                                </span>
                                @error('photo') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bio-collapse" x-data="{ open: true }" :class="{ 'is-open': open }">
                    <button type="button" class="bio-collapse__toggle" @click="open = !open" :aria-expanded="open.toString()">
                        <span>Passport details</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="bio-collapse__body" x-show="open">
                        <div class="bio-form__grid">
                            <label>
                                <span class="bio-form__label">Passport no. <span class="bio-required">*</span></span>
                                <input type="text" wire:model="passport_number" required>
                                @error('passport_number') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Date of issue <span class="bio-required">*</span></span>
                                <input type="date" wire:model="passport_issue_date" required>
                                @error('passport_issue_date') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                <span class="bio-form__label">Date of expiry <span class="bio-required">*</span></span>
                                <input type="date" wire:model="passport_expiry" required>
                                @error('passport_expiry') <span class="master-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bio-collapse" x-data="{ open: false }" :class="{ 'is-open': open }">
                    <button type="button" class="bio-collapse__toggle" @click="open = !open" :aria-expanded="open.toString()">
                        <span>Education</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="bio-collapse__body" x-show="open" x-cloak>
                        <div class="bio-form__grid bio-form__grid--half">
                            <label>Degree <input type="text" wire:model="degree"></label>
                            <label>Completion year <input type="text" wire:model="degree_year" maxlength="4"></label>
                            <label>Certification <input type="text" wire:model="certification"></label>
                            <label>Completion year <input type="text" wire:model="certification_year" maxlength="4"></label>
                            <label class="bio-form__full">Board / University <input type="text" wire:model="board_university"></label>
                            <label class="bio-form__full">Languages <input type="text" wire:model="languages"></label>
                            <label class="bio-form__full">Total experience <input type="text" wire:model="total_experience"></label>
                        </div>
                    </div>
                </div>

                <div class="bio-collapse" x-data="{ open: true }" :class="{ 'is-open': open }">
                    <button type="button" class="bio-collapse__toggle" @click="open = !open" :aria-expanded="open.toString()">
                        <span>Case assignment</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="bio-collapse__body" x-show="open">
                        <div class="bio-form__grid bio-form__grid--half">
                            <label>
                                Source
                                <select wire:model.live="source">
                                    <option value="direct">Direct</option>
                                    <option value="referred">Referred (Care Off)</option>
                                </select>
                            </label>
                            @if ($source === 'referred')
                                <label>
                                    <span class="bio-form__label">Care Off <span class="bio-required">*</span></span>
                                    @include('partials.searchable-select', [
                                        'options' => $careOffs,
                                        'wireModel' => 'care_off_id',
                                        'selectedId' => $care_off_id,
                                        'placeholder' => 'Select care off',
                                        'searchPlaceholder' => 'Search care off...',
                                        'emptyText' => 'No care off found',
                                    ])
                                    @error('care_off_id') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                            @endif
                            <label>
                                Company
                                @include('partials.searchable-select', [
                                    'options' => $companies,
                                    'wireModel' => 'company_id',
                                    'selectedId' => $company_id,
                                    'placeholder' => 'Select company',
                                    'searchPlaceholder' => 'Search company...',
                                    'emptyText' => 'No company found',
                                ])
                            </label>
                            <label>
                                Process status
                                @include('partials.searchable-select', [
                                    'options' => $processStatuses,
                                    'wireModel' => 'process_status_id',
                                    'selectedId' => $process_status_id,
                                    'placeholder' => 'Select status',
                                    'searchPlaceholder' => 'Search status...',
                                    'emptyText' => 'No status found',
                                ])
                            </label>
                            <label>
                                Overall status
                                <select wire:model="overall_status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="bio-collapse" x-data="{ open: true }" :class="{ 'is-open': open }">
                    <button type="button" class="bio-collapse__toggle" @click="open = !open" :aria-expanded="open.toString()">
                        <span>Documents checklist</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    <div class="bio-collapse__body" x-show="open">
                        <p class="bio-doc-note muted">
                            Private files only. View opens through a protected link. Max {{ $maxUploadMb }} MB · pdf / jpg / png.
                        </p>
                        <div class="table-wrap">
                            <table class="data-table bio-doc-table">
                                <thead>
                                    <tr>
                                        <th>Document</th>
                                        <th>Current</th>
                                        <th>Upload / replace</th>
                                        <th>Not required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($documents as $doc)
                                        @php
                                            $existing = $existingDocs[$doc['key']] ?? null;
                                            $hasFile = filled($existing['file_path'] ?? null);
                                        @endphp
                                        <tr wire:key="edit-doc-{{ $doc['key'] }}">
                                            <td>{{ $doc['label'] }}</td>
                                            <td>
                                                @if ($hasFile)
                                                    <a
                                                        class="bio-doc-link"
                                                        href="{{ route('clients.documents.show', [$clientId, $doc['key']]) }}"
                                                        target="_blank"
                                                        rel="noopener"
                                                    >
                                                        {{ $existing['original_name'] ?: 'View file' }}
                                                    </a>
                                                @elseif ($existing['is_not_required'] ?? false)
                                                    <span class="muted">N/A</span>
                                                @else
                                                    <span class="muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="bio-file-placeholder bio-file-placeholder--inline">
                                                    <input
                                                        type="file"
                                                        wire:model="doc_files.{{ $doc['key'] }}"
                                                        accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                                        @disabled($doc_not_required[$doc['key']] ?? false)
                                                    >
                                                </span>
                                                @error('doc_files.'.$doc['key']) <span class="master-error">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <label class="master-check master-check--inline">
                                                    <input type="checkbox" wire:model.live="doc_not_required.{{ $doc['key'] }}">
                                                    <span>N/A</span>
                                                </label>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bio-form__actions">
                    <button type="button" class="button button--ghost" wire:click="backToList">Cancel</button>
                    <button type="submit" class="button button--soft">Save changes</button>
                </div>
            </form>
        </section>
    @endif
</div>
