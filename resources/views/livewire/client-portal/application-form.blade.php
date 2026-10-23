<div>
    @if ($successMessage !== '')
        <div
            class="cp-app-toast"
            wire:key="portal-toast-{{ $toastVersion }}"
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4500)"
            x-show="show"
            x-transition.opacity.duration.200ms
            role="status"
            aria-live="polite"
        >
            <div>
                <small>Saved</small>
                <strong>{{ $successMessage }}</strong>
            </div>
            <button type="button" class="cp-app-toast__close" @click="show = false" aria-label="Dismiss">&times;</button>
        </div>
    @endif

    <section class="page-hero page-hero--candidates">
        <div class="site-wrap page-hero__inner">
            <span class="section-kicker">Applicant portal</span>
            <h1>{{ $clientId ? 'Update your bio data.' : 'Fill your bio data.' }}</h1>
            <p>Complete each section below. Office staff will set your process status after review.</p>
        </div>
    </section>

    <section class="page-section page-section--soft">
        <div class="site-wrap cp-app">
            <div class="contact-form cp-app__intro">
                <h2>Preferred role</h2>

                @if ($appliedJobTitle)
                    <div class="cp-app-applied">
                        <strong>Applying for</strong>
                        <span>{{ $appliedJobTitle }}@if ($appliedJobCompany) · {{ $appliedJobCompany }}@endif</span>
                    </div>
                @endif

                <div class="form-row">
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

            <section class="cp-app-section {{ $openPersonal ? 'is-open' : '' }}">
                <button type="button" class="cp-app-section__toggle" wire:click="toggleSection('personal')">
                    <span><strong>01</strong> Personal details</span>
                    <span class="cp-app-chevron" aria-hidden="true"></span>
                </button>
                @if ($openPersonal)
                    <div class="cp-app-section__body contact-form">
                        <label>
                            Name *
                            <input type="text" wire:model="full_name" placeholder="As on passport">
                            @error('full_name') <span class="ep-field-error">{{ $message }}</span> @enderror
                        </label>
                        <div class="form-row">
                            <label>
                                Height
                                <input type="text" wire:model="height" placeholder="e.g. 5'8&quot;">
                            </label>
                            <label>
                                Weight
                                <input type="text" wire:model="weight" placeholder="e.g. 70 kg">
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Father's name *
                                <input type="text" wire:model="father_name">
                                @error('father_name') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Mother's name *
                                <input type="text" wire:model="mother_name">
                                @error('mother_name') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Gender *
                                <select wire:model="gender">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                @error('gender') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Marital status *
                                <select wire:model="marital_status">
                                    <option value="">Select</option>
                                    <option value="single">Single</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                </select>
                                @error('marital_status') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Date of birth *
                                <input type="date" wire:model.live="date_of_birth">
                                @error('date_of_birth') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Age
                                <input type="text" value="{{ $age }}" readonly tabindex="-1">
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Religion *
                                <input type="text" wire:model="religion">
                                @error('religion') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                ID number (CNIC) *
                                <input type="text" wire:model="cnic" placeholder="e.g. 35202-1234567-1">
                                @error('cnic') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <label>
                            Place of birth
                            <input type="text" wire:model="place_of_birth">
                        </label>
                        <div class="form-row">
                            <label>
                                Phone
                                <input type="tel" wire:model="phone">
                            </label>
                            <label>
                                Email
                                <input type="email" wire:model="email">
                            </label>
                        </div>
                        <label>
                            Permanent address
                            <input type="text" wire:model="address">
                        </label>
                        <div class="form-row">
                            <label>
                                Police station
                                <input type="text" wire:model="police_station">
                            </label>
                            <label>
                                District
                                <input type="text" wire:model="district">
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Medical fitness
                                <select wire:model="medical_fitness">
                                    <option value="">Select</option>
                                    <option value="fit">Fit</option>
                                    <option value="unfit">Unfit</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </label>
                            <label>
                                Photo
                                <input type="file" wire:model="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                <span class="cp-app-hint">JPG/PNG · max {{ $maxUploadMb }} MB
                                    @if ($photo_path) · current photo on file @endif
                                </span>
                                <span wire:loading wire:target="photo" class="cp-app-hint">Uploading…</span>
                                @error('photo') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <button type="button" class="btn btn-accent" wire:click="continuePersonal">Continue <span>&rarr;</span></button>
                    </div>
                @endif
            </section>

            <section class="cp-app-section {{ $openPassport ? 'is-open' : '' }} {{ $unlockedPassport ? '' : 'is-locked' }}">
                <button type="button" class="cp-app-section__toggle" wire:click="toggleSection('passport')" @disabled(! $unlockedPassport)>
                    <span><strong>02</strong> Passport details</span>
                    <span class="cp-app-chevron" aria-hidden="true"></span>
                </button>
                @if ($openPassport)
                    <div class="cp-app-section__body contact-form">
                        <label>
                            Passport no. *
                            <input type="text" wire:model="passport_number">
                            @error('passport_number') <span class="ep-field-error">{{ $message }}</span> @enderror
                        </label>
                        <div class="form-row">
                            <label>
                                Date of issue *
                                <input type="date" wire:model="passport_issue_date">
                                @error('passport_issue_date') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                            <label>
                                Date of expiry *
                                <input type="date" wire:model="passport_expiry">
                                @error('passport_expiry') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        </div>
                        <button type="button" class="btn btn-accent" wire:click="continuePassport">Continue <span>&rarr;</span></button>
                    </div>
                @endif
            </section>

            <section class="cp-app-section {{ $openEducation ? 'is-open' : '' }} {{ $unlockedEducation ? '' : 'is-locked' }}">
                <button type="button" class="cp-app-section__toggle" wire:click="toggleSection('education')" @disabled(! $unlockedEducation)>
                    <span><strong>03</strong> Education &amp; experience</span>
                    <span class="cp-app-chevron" aria-hidden="true"></span>
                </button>
                @if ($openEducation)
                    <div class="cp-app-section__body contact-form">
                        <div class="form-row">
                            <label>
                                Degree
                                <input type="text" wire:model="degree">
                            </label>
                            <label>
                                Year
                                <input type="text" wire:model="degree_year" maxlength="4" placeholder="2018">
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Certification
                                <input type="text" wire:model="certification">
                            </label>
                            <label>
                                Year
                                <input type="text" wire:model="certification_year" maxlength="4" placeholder="2019">
                            </label>
                        </div>
                        <div class="form-row">
                            <label>
                                Board / University
                                <input type="text" wire:model="board_university">
                            </label>
                            <label>
                                Languages
                                <input type="text" wire:model="languages" placeholder="e.g. Urdu, English">
                            </label>
                        </div>
                        <label>
                            Total experience
                            <input type="text" wire:model="total_experience" placeholder="e.g. 5 years">
                        </label>

                        <p class="cp-app-hint cp-app-hint--block">Work history (up to 5 rows)</p>
                        @foreach ($experiences as $index => $row)
                            <div class="cp-app-exp" wire:key="portal-exp-{{ $index }}">
                                <div class="form-row">
                                    <label>
                                        From – To
                                        <input type="text" wire:model="experiences.{{ $index }}.from_to" placeholder="From - To">
                                    </label>
                                    <label>
                                        Company
                                        <input type="text" wire:model="experiences.{{ $index }}.company" placeholder="Company">
                                    </label>
                                </div>
                                <div class="form-row">
                                    <label>
                                        Project
                                        <input type="text" wire:model="experiences.{{ $index }}.project" placeholder="Project">
                                    </label>
                                    <label>
                                        Country
                                        <input type="text" wire:model="experiences.{{ $index }}.country" placeholder="Country">
                                    </label>
                                </div>
                                <label>
                                    Job title
                                    <input type="text" wire:model="experiences.{{ $index }}.job_title" placeholder="Job title">
                                </label>
                            </div>
                        @endforeach

                        <button type="button" class="btn btn-accent" wire:click="continueEducation">Continue <span>&rarr;</span></button>
                    </div>
                @endif
            </section>

            <section class="cp-app-section {{ $openCase ? 'is-open' : '' }} {{ $unlockedCase ? '' : 'is-locked' }}">
                <button type="button" class="cp-app-section__toggle" wire:click="toggleSection('case')" @disabled(! $unlockedCase)>
                    <span><strong>04</strong> Application details</span>
                    <span class="cp-app-chevron" aria-hidden="true"></span>
                </button>
                @if ($openCase)
                    <div class="cp-app-section__body contact-form">
                        <div class="form-row">
                            <label>
                                Source
                                <select wire:model.live="source">
                                    <option value="direct">Direct</option>
                                    <option value="referred">Referred (Care Off)</option>
                                </select>
                            </label>
                            <label>
                                Preferred company
                                <select wire:model="company_id">
                                    <option value="">Select company</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                        @if ($source === 'referred')
                            <label>
                                Care Off *
                                <select wire:model="care_off_id">
                                    <option value="">Select care off</option>
                                    @foreach ($careOffs as $careOff)
                                        <option value="{{ $careOff->id }}">{{ $careOff->name }}</option>
                                    @endforeach
                                </select>
                                @error('care_off_id') <span class="ep-field-error">{{ $message }}</span> @enderror
                            </label>
                        @endif
                        <button type="button" class="btn btn-accent" wire:click="continueCase">Continue <span>&rarr;</span></button>
                    </div>
                @endif
            </section>

            <section class="cp-app-section {{ $openDocuments ? 'is-open' : '' }} {{ $unlockedDocuments ? '' : 'is-locked' }}">
                <button type="button" class="cp-app-section__toggle" wire:click="toggleSection('documents')" @disabled(! $unlockedDocuments)>
                    <span><strong>05</strong> Documents</span>
                    <span class="cp-app-chevron" aria-hidden="true"></span>
                </button>
                @if ($openDocuments)
                    <div class="cp-app-section__body contact-form">
                        <p class="cp-app-hint cp-app-hint--block">Upload a file or mark N/A. Max {{ $maxUploadMb }} MB each (PDF/JPG/PNG).</p>

                        @foreach ($documents as $doc)
                            <div class="cp-app-doc" wire:key="portal-doc-{{ $doc['key'] }}">
                                <strong>{{ $doc['label'] }}</strong>
                                @if (! empty($existing_docs[$doc['key']]))
                                    <span class="cp-app-hint">On file: {{ $existing_docs[$doc['key']] }}</span>
                                @endif
                                <label class="cp-app-check">
                                    <input type="checkbox" wire:model.live="doc_not_required.{{ $doc['key'] }}">
                                    Not applicable
                                </label>
                                @if (! ($doc_not_required[$doc['key']] ?? false))
                                    <input type="file" wire:model="doc_files.{{ $doc['key'] }}" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                                    <span wire:loading wire:target="doc_files.{{ $doc['key'] }}" class="cp-app-hint">Uploading…</span>
                                    @error('doc_files.'.$doc['key']) <span class="ep-field-error">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        @endforeach

                        <button
                            type="button"
                            class="btn btn-accent"
                            wire:click="saveApplication"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading.remove wire:target="saveApplication">Save application <span>&rarr;</span></span>
                            <span wire:loading wire:target="saveApplication">Saving…</span>
                        </button>
                    </div>
                @endif
            </section>
        </div>
    </section>
</div>
