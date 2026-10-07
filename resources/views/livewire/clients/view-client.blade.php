<div class="bio-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view clients.</p>
        </div>
    @elseif ($notFound || ! $client)
        <div class="panel empty-card">
            <h3>Client not found</h3>
            <button type="button" class="button button--soft" wire:click="backToList">Back to list</button>
        </div>
    @else
        <section class="panel bio-panel">
            <div class="bio-toolbar bio-toolbar--simple">
                <button type="button" class="text-button back-button" wire:click="backToList">
                    <span aria-hidden="true">&larr;</span> Back to list
                </button>
                <p class="bio-section-title">View client</p>
            </div>

            <div class="bio-form">
                <fieldset class="bio-section">
                    <legend>Job</legend>
                    <dl class="bio-view-grid">
                        <div><dt>Trade</dt><dd>{{ $client->trade?->name ?: '—' }}</dd></div>
                        <div><dt>Country</dt><dd>{{ $client->country?->name ?: '—' }}</dd></div>
                    </dl>
                </fieldset>

                <fieldset class="bio-section">
                    <legend>Personal details</legend>
                    <dl class="bio-view-grid">
                        <div class="bio-view-grid__full"><dt>Name</dt><dd>{{ $client->full_name }}</dd></div>
                        <div><dt>Height</dt><dd>{{ $client->height ?: '—' }}</dd></div>
                        <div><dt>Weight</dt><dd>{{ $client->weight ?: '—' }}</dd></div>
                        <div><dt>Father's name</dt><dd>{{ $client->father_name }}</dd></div>
                        <div><dt>Gender</dt><dd>{{ $client->gender ?: '—' }}</dd></div>
                        <div><dt>Mother's name</dt><dd>{{ $client->mother_name }}</dd></div>
                        <div><dt>Date of birth</dt><dd>{{ optional($client->date_of_birth)->format('Y-m-d') ?: '—' }}</dd></div>
                        <div><dt>Age</dt><dd>{{ $client->age ?? '—' }}</dd></div>
                        <div><dt>Marital status</dt><dd>{{ $client->marital_status ?: '—' }}</dd></div>
                        <div><dt>Religion</dt><dd>{{ $client->religion }}</dd></div>
                        <div><dt>CNIC</dt><dd>{{ $client->cnic }}</dd></div>
                        <div><dt>Place of birth</dt><dd>{{ $client->place_of_birth ?: '—' }}</dd></div>
                        <div><dt>Phone</dt><dd>{{ $client->phone ?: '—' }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $client->email ?: '—' }}</dd></div>
                        <div class="bio-view-grid__full"><dt>Address</dt><dd>{{ $client->address ?: '—' }}</dd></div>
                        <div><dt>Police station</dt><dd>{{ $client->police_station ?: '—' }}</dd></div>
                        <div><dt>District</dt><dd>{{ $client->district ?: '—' }}</dd></div>
                        <div><dt>Medical fitness</dt><dd>{{ $client->medical_fitness ?: '—' }}</dd></div>
                        <div class="bio-view-grid__full">
                            <dt>Photo</dt>
                            <dd>
                                @if ($client->photo_path)
                                    <a class="bio-doc-link" href="{{ route('clients.photo.show', $client->id) }}" target="_blank" rel="noopener">View photo</a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                    </dl>
                </fieldset>

                <fieldset class="bio-section">
                    <legend>Passport details</legend>
                    <dl class="bio-view-grid">
                        <div><dt>Passport no.</dt><dd>{{ $client->passport_number }}</dd></div>
                        <div><dt>Date of issue</dt><dd>{{ optional($client->passport_issue_date)->format('Y-m-d') ?: '—' }}</dd></div>
                        <div><dt>Date of expiry</dt><dd>{{ optional($client->passport_expiry)->format('Y-m-d') ?: '—' }}</dd></div>
                    </dl>
                </fieldset>

                <fieldset class="bio-section">
                    <legend>Education</legend>
                    <dl class="bio-view-grid">
                        <div><dt>Degree</dt><dd>{{ $client->degree ?: '—' }}</dd></div>
                        <div><dt>Degree year</dt><dd>{{ $client->degree_year ?: '—' }}</dd></div>
                        <div><dt>Certification</dt><dd>{{ $client->certification ?: '—' }}</dd></div>
                        <div><dt>Certification year</dt><dd>{{ $client->certification_year ?: '—' }}</dd></div>
                        <div class="bio-view-grid__full"><dt>Board / University</dt><dd>{{ $client->board_university ?: '—' }}</dd></div>
                        <div class="bio-view-grid__full"><dt>Languages</dt><dd>{{ $client->languages ?: '—' }}</dd></div>
                        <div class="bio-view-grid__full"><dt>Total experience</dt><dd>{{ $client->total_experience ?: '—' }}</dd></div>
                    </dl>
                </fieldset>

                <fieldset class="bio-section">
                    <legend>Case assignment</legend>
                    <dl class="bio-view-grid">
                        <div><dt>Source</dt><dd>{{ $client->source === 'referred' ? 'Referred' : 'Direct' }}</dd></div>
                        <div><dt>Care Off</dt><dd>{{ $client->careOff?->name ?: '—' }}</dd></div>
                        <div><dt>Company</dt><dd>{{ $client->company?->name ?: '—' }}</dd></div>
                        <div><dt>Process status</dt><dd>{{ $client->processStatus?->name ?: '—' }}</dd></div>
                        <div><dt>Overall status</dt><dd>{{ $client->overall_status === 'active' ? 'Active' : 'Inactive' }}</dd></div>
                    </dl>
                </fieldset>

                <fieldset class="bio-section">
                    <legend>Documents</legend>
                    <div class="table-wrap">
                        <table class="data-table bio-doc-table">
                            <thead>
                                <tr>
                                    <th>Document</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documentRows as $row)
                                    @php $doc = $docsByKey[$row['key']] ?? null; @endphp
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>
                                            @if ($doc && $doc->file_path)
                                                <a
                                                    class="bio-doc-link"
                                                    href="{{ route('clients.documents.show', [$client->id, $row['key']]) }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                >
                                                    {{ $doc->original_name ?: 'View file' }}
                                                </a>
                                            @elseif ($doc && $doc->is_not_required)
                                                <span class="muted">N/A</span>
                                            @else
                                                <span class="muted">Not provided</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
        </section>
    @endif
</div>
