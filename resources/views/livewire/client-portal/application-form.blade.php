<div>
    <div class="cp-topbar">
        <a href="{{ route('client-portal.home') }}" style="color:#fff;text-decoration:none;font-weight:800;">← Home</a>
        <form method="POST" action="{{ route('client-portal.logout') }}">
            @csrf
            <button class="cp-link-btn" type="submit">Sign out</button>
        </form>
    </div>

    <header class="cp-brand">
        <img src="{{ asset('images/federal-group-logo.png') }}" alt="Federal Group of Companies">
        <p>{{ $clientId ? 'Edit application' : 'New application' }}</p>
    </header>

    @if ($successMessage !== '')
        <div
            class="cp-toast"
            wire:key="portal-toast-{{ $toastVersion }}"
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4500)"
            x-show="show"
            x-transition.opacity.duration.200ms
            role="status"
            aria-live="polite"
        >
            <div class="cp-toast__copy">
                <small>Success</small>
                <strong>{{ $successMessage }}</strong>
            </div>
            <button type="button" class="cp-toast__close" @click="show = false" aria-label="Dismiss">&times;</button>
            <span class="cp-toast__progress" aria-hidden="true"></span>
        </div>
    @endif

    <main class="cp-card cp-app">
        <h1 class="cp-title">{{ $clientId ? 'Update your bio data' : 'Fill your bio data' }}</h1>
        <p class="cp-intro">Complete each section. Office staff will set your process status after review.</p>

        <div class="cp-job">
            <div class="cp-field">
                <label for="trade_id">Job title</label>
                <select id="trade_id" wire:model="trade_id">
                    <option value="">Select trade</option>
                    @foreach ($trades as $trade)
                        <option value="{{ $trade->id }}">{{ $trade->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="cp-field">
                <label for="country_id">Country</label>
                <select id="country_id" wire:model="country_id">
                    <option value="">Select country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Personal --}}
        <section class="cp-section {{ $openPersonal ? 'is-open' : '' }}">
            <button type="button" class="cp-section__toggle" wire:click="toggleSection('personal')">
                <span>1. Personal details</span>
                <span class="cp-chevron"></span>
            </button>
            @if ($openPersonal)
                <div class="cp-section__body">
                    <div class="cp-field">
                        <label>Name *</label>
                        <input type="text" wire:model="full_name" placeholder="As on passport">
                        @error('full_name') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Height</label>
                            <input type="text" wire:model="height" placeholder="e.g. 5'8&quot;">
                        </div>
                        <div class="cp-field">
                            <label>Weight</label>
                            <input type="text" wire:model="weight" placeholder="e.g. 70 kg">
                        </div>
                    </div>
                    <div class="cp-field">
                        <label>Father's name *</label>
                        <input type="text" wire:model="father_name">
                        @error('father_name') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-field">
                        <label>Mother's name *</label>
                        <input type="text" wire:model="mother_name">
                        @error('mother_name') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Gender *</label>
                            <select wire:model="gender">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            @error('gender') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="cp-field">
                            <label>Marital status *</label>
                            <select wire:model="marital_status">
                                <option value="">Select</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                            @error('marital_status') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Date of birth *</label>
                            <input type="date" wire:model.live="date_of_birth">
                            @error('date_of_birth') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="cp-field">
                            <label>Age</label>
                            <input type="text" value="{{ $age }}" readonly tabindex="-1">
                        </div>
                    </div>
                    <div class="cp-field">
                        <label>Religion *</label>
                        <input type="text" wire:model="religion">
                        @error('religion') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-field">
                        <label>ID number (CNIC) *</label>
                        <input type="text" wire:model="cnic" placeholder="e.g. 35202-1234567-1">
                        @error('cnic') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-field">
                        <label>Place of birth</label>
                        <input type="text" wire:model="place_of_birth">
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Phone</label>
                            <input type="tel" wire:model="phone">
                        </div>
                        <div class="cp-field">
                            <label>Email</label>
                            <input type="email" wire:model="email">
                        </div>
                    </div>
                    <div class="cp-field">
                        <label>Permanent address</label>
                        <input type="text" wire:model="address">
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Police station</label>
                            <input type="text" wire:model="police_station">
                        </div>
                        <div class="cp-field">
                            <label>District</label>
                            <input type="text" wire:model="district">
                        </div>
                    </div>
                    <div class="cp-field">
                        <label>Medical fitness</label>
                        <select wire:model="medical_fitness">
                            <option value="">Select</option>
                            <option value="fit">Fit</option>
                            <option value="unfit">Unfit</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="cp-field">
                        <label>Photo</label>
                        <input type="file" wire:model="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                        <small class="cp-hint">JPG/PNG · max {{ $maxUploadMb }} MB
                            @if ($photo_path) · current photo on file @endif
                        </small>
                        <div wire:loading wire:target="photo" class="cp-hint">Uploading…</div>
                        @error('photo') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" class="cp-btn" wire:click="continuePersonal">Continue</button>
                </div>
            @endif
        </section>

        {{-- Passport --}}
        <section class="cp-section {{ $openPassport ? 'is-open' : '' }} {{ $unlockedPassport ? '' : 'is-locked' }}">
            <button type="button" class="cp-section__toggle" wire:click="toggleSection('passport')" @disabled(! $unlockedPassport)>
                <span>2. Passport details</span>
                <span class="cp-chevron"></span>
            </button>
            @if ($openPassport)
                <div class="cp-section__body">
                    <div class="cp-field">
                        <label>Passport no. *</label>
                        <input type="text" wire:model="passport_number">
                        @error('passport_number') <p class="cp-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Date of issue *</label>
                            <input type="date" wire:model="passport_issue_date">
                            @error('passport_issue_date') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="cp-field">
                            <label>Date of expiry *</label>
                            <input type="date" wire:model="passport_expiry">
                            @error('passport_expiry') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <button type="button" class="cp-btn" wire:click="continuePassport">Continue</button>
                </div>
            @endif
        </section>

        {{-- Education --}}
        <section class="cp-section {{ $openEducation ? 'is-open' : '' }} {{ $unlockedEducation ? '' : 'is-locked' }}">
            <button type="button" class="cp-section__toggle" wire:click="toggleSection('education')" @disabled(! $unlockedEducation)>
                <span>3. Education & experience</span>
                <span class="cp-chevron"></span>
            </button>
            @if ($openEducation)
                <div class="cp-section__body">
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Degree</label>
                            <input type="text" wire:model="degree">
                        </div>
                        <div class="cp-field">
                            <label>Year</label>
                            <input type="text" wire:model="degree_year" maxlength="4" placeholder="2018">
                        </div>
                    </div>
                    <div class="cp-grid">
                        <div class="cp-field">
                            <label>Certification</label>
                            <input type="text" wire:model="certification">
                        </div>
                        <div class="cp-field">
                            <label>Year</label>
                            <input type="text" wire:model="certification_year" maxlength="4" placeholder="2019">
                        </div>
                    </div>
                    <div class="cp-field">
                        <label>Board / University</label>
                        <input type="text" wire:model="board_university">
                    </div>
                    <div class="cp-field">
                        <label>Languages</label>
                        <input type="text" wire:model="languages" placeholder="e.g. Urdu, English">
                    </div>
                    <div class="cp-field">
                        <label>Total experience</label>
                        <input type="text" wire:model="total_experience" placeholder="e.g. 5 years">
                    </div>

                    <p class="cp-hint" style="margin:4px 0 10px;">Work history (up to 5 rows)</p>
                    @foreach ($experiences as $index => $row)
                        <div class="cp-exp" wire:key="portal-exp-{{ $index }}">
                            <input type="text" wire:model="experiences.{{ $index }}.from_to" placeholder="From - To">
                            <input type="text" wire:model="experiences.{{ $index }}.company" placeholder="Company">
                            <input type="text" wire:model="experiences.{{ $index }}.project" placeholder="Project">
                            <input type="text" wire:model="experiences.{{ $index }}.country" placeholder="Country">
                            <input type="text" wire:model="experiences.{{ $index }}.job_title" placeholder="Job title">
                        </div>
                    @endforeach

                    <button type="button" class="cp-btn" wire:click="continueEducation">Continue</button>
                </div>
            @endif
        </section>

        {{-- Case --}}
        <section class="cp-section {{ $openCase ? 'is-open' : '' }} {{ $unlockedCase ? '' : 'is-locked' }}">
            <button type="button" class="cp-section__toggle" wire:click="toggleSection('case')" @disabled(! $unlockedCase)>
                <span>4. Application details</span>
                <span class="cp-chevron"></span>
            </button>
            @if ($openCase)
                <div class="cp-section__body">
                    <div class="cp-field">
                        <label>Source</label>
                        <select wire:model.live="source">
                            <option value="direct">Direct</option>
                            <option value="referred">Referred (Care Off)</option>
                        </select>
                    </div>
                    @if ($source === 'referred')
                        <div class="cp-field">
                            <label>Care Off *</label>
                            <select wire:model="care_off_id">
                                <option value="">Select care off</option>
                                @foreach ($careOffs as $careOff)
                                    <option value="{{ $careOff->id }}">{{ $careOff->name }}</option>
                                @endforeach
                            </select>
                            @error('care_off_id') <p class="cp-error">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    <div class="cp-field">
                        <label>Preferred company</label>
                        <select wire:model="company_id">
                            <option value="">Select company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" class="cp-btn" wire:click="continueCase">Continue</button>
                </div>
            @endif
        </section>

        {{-- Documents --}}
        <section class="cp-section {{ $openDocuments ? 'is-open' : '' }} {{ $unlockedDocuments ? '' : 'is-locked' }}">
            <button type="button" class="cp-section__toggle" wire:click="toggleSection('documents')" @disabled(! $unlockedDocuments)>
                <span>5. Documents</span>
                <span class="cp-chevron"></span>
            </button>
            @if ($openDocuments)
                <div class="cp-section__body">
                    <p class="cp-hint" style="margin-bottom:12px;">Upload a file or mark N/A. Max {{ $maxUploadMb }} MB each (PDF/JPG/PNG).</p>

                    @foreach ($documents as $doc)
                        <div class="cp-doc" wire:key="portal-doc-{{ $doc['key'] }}">
                            <strong>{{ $doc['label'] }}</strong>
                            @if (! empty($existing_docs[$doc['key']]))
                                <small class="cp-hint">On file: {{ $existing_docs[$doc['key']] }}</small>
                            @endif
                            <label class="cp-check" style="margin:8px 0;">
                                <input type="checkbox" wire:model.live="doc_not_required.{{ $doc['key'] }}">
                                Not applicable
                            </label>
                            @if (! ($doc_not_required[$doc['key']] ?? false))
                                <input type="file" wire:model="doc_files.{{ $doc['key'] }}" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                                <div wire:loading wire:target="doc_files.{{ $doc['key'] }}" class="cp-hint">Uploading…</div>
                                @error('doc_files.'.$doc['key']) <p class="cp-error">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    @endforeach

                    <button
                        type="button"
                        class="cp-btn"
                        wire:click="saveApplication"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="saveApplication">Save application</span>
                        <span wire:loading wire:target="saveApplication">Saving…</span>
                    </button>
                </div>
            @endif
        </section>
    </main>
</div>
