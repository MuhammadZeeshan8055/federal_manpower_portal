<div class="bio-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view clients.</p>
        </div>
    @else
        @if ($successMessage !== '')
            <div
                class="portal-toast portal-toast--{{ $toastType }}"
                wire:key="client-toast-{{ $toastVersion }}"
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
                <div class="bio-print-actions">
                    <button type="button" class="button button--ghost" disabled title="Available after fields are confirmed">
                        Print customer
                    </button>
                    <button type="button" class="button button--ghost" disabled title="Available after fields are confirmed">
                        Print office
                    </button>
                </div>
            </div>

            <form class="bio-form" onsubmit="return false">
                <div class="bio-collapse {{ $openPersonal ? 'is-open' : '' }}">
                    <button
                        type="button"
                        class="bio-collapse__toggle"
                        wire:click="toggleSection('personal')"
                        aria-expanded="{{ $openPersonal ? 'true' : 'false' }}"
                    >
                        <span>Personal details</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    @if ($openPersonal)
                        <div class="bio-collapse__body">
                            <div class="bio-form__grid">
                                <label class="bio-form__full">
                                    <span class="bio-form__label">Name <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="full_name" placeholder="As on passport" required>
                                    @error('full_name') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    Height
                                    <input type="text" wire:model="height" placeholder="e.g. 5'8&quot;">
                                </label>
                                <label>
                                    Weight
                                    <input type="text" wire:model="weight" placeholder="e.g. 70 kg">
                                </label>
                                <label>
                                    <span class="bio-form__label">Father's name <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="father_name" required>
                                    @error('father_name') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">Gender <span class="bio-required" title="Required">*</span></span>
                                    <select wire:model="gender" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    @error('gender') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">Mother's name <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="mother_name" required>
                                    @error('mother_name') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">Date of birth <span class="bio-required" title="Required">*</span></span>
                                    <input type="date" wire:model.live="date_of_birth" required>
                                    @error('date_of_birth') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    Age
                                    <input type="text" value="{{ $age }}" readonly placeholder="Auto" tabindex="-1">
                                </label>
                                <label>
                                    <span class="bio-form__label">Marital status <span class="bio-required" title="Required">*</span></span>
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
                                    <span class="bio-form__label">Religion <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="religion" required>
                                    @error('religion') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">ID number (CNIC) <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="cnic" placeholder="e.g. 35202-1234567-1" required>
                                    @error('cnic') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    Place of birth
                                    <input type="text" wire:model="place_of_birth">
                                </label>
                                <label>
                                    Phone
                                    <input type="tel" wire:model="phone">
                                </label>
                                <label>
                                    Email
                                    <input type="email" wire:model="email">
                                </label>
                                <label class="bio-form__full">
                                    Permanent address
                                    <input type="text" wire:model="address">
                                </label>
                                <label>
                                    Police station
                                    <input type="text" wire:model="police_station">
                                </label>
                                <label>
                                    District
                                    <input type="text" wire:model="district">
                                </label>
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
                                    <span class="bio-file-placeholder">
                                        <input type="file" wire:model="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" aria-label="Client photo">
                                        <small>JPG/PNG · max {{ $maxUploadMb }} MB · private</small>
                                    </span>
                                    @error('photo') <span class="master-error">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="photo" class="muted" style="font-size:0.7rem;">Uploading…</div>
                                </label>
                            </div>
                            <div class="bio-form__actions">
                                <button type="button" class="button button--soft" wire:click="continuePersonal">
                                    Continue
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bio-collapse {{ $openPassport ? 'is-open' : '' }} {{ $unlockedPassport ? '' : 'is-locked' }}">
                    <button
                        type="button"
                        class="bio-collapse__toggle"
                        wire:click="toggleSection('passport')"
                        aria-expanded="{{ $openPassport ? 'true' : 'false' }}"
                        @disabled(! $unlockedPassport)
                    >
                        <span>Passport details</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    @if ($openPassport)
                        <div class="bio-collapse__body">
                            <div class="bio-form__grid">
                                <label>
                                    <span class="bio-form__label">Passport no. <span class="bio-required" title="Required">*</span></span>
                                    <input type="text" wire:model="passport_number" required>
                                    @error('passport_number') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">Date of issue <span class="bio-required" title="Required">*</span></span>
                                    <input type="date" wire:model="passport_issue_date" required>
                                    @error('passport_issue_date') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                                <label>
                                    <span class="bio-form__label">Date of expiry <span class="bio-required" title="Required">*</span></span>
                                    <input type="date" wire:model="passport_expiry" required>
                                    @error('passport_expiry') <span class="master-error">{{ $message }}</span> @enderror
                                </label>
                            </div>
                            <div class="bio-form__actions">
                                <button type="button" class="button button--soft" wire:click="continuePassport">
                                    Continue
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bio-collapse {{ $openEducation ? 'is-open' : '' }} {{ $unlockedEducation ? '' : 'is-locked' }}">
                    <button
                        type="button"
                        class="bio-collapse__toggle"
                        wire:click="toggleSection('education')"
                        aria-expanded="{{ $openEducation ? 'true' : 'false' }}"
                        @disabled(! $unlockedEducation)
                    >
                        <span>Education</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    @if ($openEducation)
                        <div class="bio-collapse__body">
                            <div class="bio-form__grid bio-form__grid--half">
                                <label>
                                    Degree
                                    <input type="text" wire:model="degree">
                                </label>
                                <label>
                                    Completion year
                                    <input type="text" wire:model="degree_year" placeholder="e.g. 2018" maxlength="4">
                                </label>
                                <label>
                                    Certification
                                    <input type="text" wire:model="certification">
                                </label>
                                <label>
                                    Completion year
                                    <input type="text" wire:model="certification_year" placeholder="e.g. 2019" maxlength="4">
                                </label>
                                <label class="bio-form__full">
                                    Board / University
                                    <input type="text" wire:model="board_university">
                                </label>
                                <label class="bio-form__full">
                                    Languages
                                    <input type="text" wire:model="languages" placeholder="e.g. Urdu, English">
                                </label>
                                <label class="bio-form__full">
                                    Total experience
                                    <input type="text" wire:model="total_experience" placeholder="e.g. 5 years">
                                </label>
                            </div>
                            <div class="bio-form__actions">
                                <button type="button" class="button button--soft" wire:click="continueEducation">
                                    Continue
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bio-collapse {{ $openCase ? 'is-open' : '' }} {{ $unlockedCase ? '' : 'is-locked' }}">
                    <button
                        type="button"
                        class="bio-collapse__toggle"
                        wire:click="toggleSection('case')"
                        aria-expanded="{{ $openCase ? 'true' : 'false' }}"
                        @disabled(! $unlockedCase)
                    >
                        <span>Case assignment</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    @if ($openCase)
                        <div class="bio-collapse__body">
                            <div class="bio-form__grid bio-form__grid--half">
                                <label>
                                    Source
                                    <select wire:model.live="source">
                                        <option value="direct">Direct</option>
                                        <option value="referred">Referred (Care Off)</option>
                                    </select>
                                    @error('source') <span class="master-error">{{ $message }}</span> @enderror
                                </label>

                                @if ($source === 'referred')
                                    <label>
                                        <span class="bio-form__label">Care Off <span class="bio-required" title="Required">*</span></span>
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
                            </div>
                            <div class="bio-form__actions">
                                <button type="button" class="button button--soft" wire:click="continueCase">
                                    Continue
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bio-collapse {{ $openDocuments ? 'is-open' : '' }} {{ $unlockedDocuments ? '' : 'is-locked' }}">
                    <button
                        type="button"
                        class="bio-collapse__toggle"
                        wire:click="toggleSection('documents')"
                        aria-expanded="{{ $openDocuments ? 'true' : 'false' }}"
                        @disabled(! $unlockedDocuments)
                    >
                        <span>Documents checklist</span>
                        <span class="bio-collapse__chevron" aria-hidden="true"></span>
                    </button>
                    @if ($openDocuments)
                        <div class="bio-collapse__body">
                            <p class="bio-doc-note muted">
                                Files go to private storage only (pdf / jpg / png, max {{ $maxUploadMb }} MB). Mark <strong>N/A</strong> when not needed.
                            </p>
                            <div class="table-wrap">
                                <table class="data-table bio-doc-table">
                                    <thead>
                                        <tr>
                                            <th>Document</th>
                                            <th>Upload</th>
                                            <th>Not required</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($documents as $doc)
                                            <tr wire:key="doc-row-{{ $doc['key'] }}">
                                                <td>{{ $doc['label'] }}</td>
                                                <td>
                                                    <span class="bio-file-placeholder bio-file-placeholder--inline">
                                                        <input
                                                            type="file"
                                                            wire:model="doc_files.{{ $doc['key'] }}"
                                                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                                                            aria-label="Upload {{ $doc['label'] }}"
                                                            @disabled($doc_not_required[$doc['key']] ?? false)
                                                        >
                                                        <small>Private</small>
                                                    </span>
                                                    @error('doc_files.'.$doc['key']) <span class="master-error">{{ $message }}</span> @enderror
                                                    <div wire:loading wire:target="doc_files.{{ $doc['key'] }}" class="muted" style="font-size:0.7rem;">Uploading…</div>
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
                            <div class="bio-form__actions">
                                <button type="button" class="button button--soft" wire:click="saveClient">
                                    Save client
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </section>
    @endif
</div>
