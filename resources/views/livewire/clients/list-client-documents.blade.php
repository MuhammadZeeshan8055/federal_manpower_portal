<div class="master-page">
    @if ($denied)
        <div class="panel empty-card">
            <h3>No access</h3>
            <p>You do not have permission to view client documents.</p>
        </div>
    @else
        <section class="panel master-list-panel">
            <div class="panel__header">
                <div>
                    <p class="panel__eyebrow">CLIENTS</p>
                    <h3>Documents</h3>
                    <p class="muted" style="margin:6px 0 0;font-size:0.75rem;">
                        Private files only — View opens through a protected link.
                    </p>
                </div>

                <label class="master-search">
                    <span class="master-search__icon" aria-hidden="true">
                        @include('partials.icon', ['name' => 'search'])
                    </span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search client, CNIC, document..."
                        aria-label="Search documents"
                    >
                </label>
            </div>

            <div class="bio-toolbar" style="border-bottom:0;margin-bottom:12px;padding-bottom:0;">
                <div class="bio-toolbar__job" style="grid-template-columns: repeat(2, minmax(140px, 200px));">
                    <label>
                        Status
                        <select wire:model.live="filter">
                            <option value="uploaded">Uploaded</option>
                            <option value="missing">Missing</option>
                            <option value="na">N/A</option>
                        </select>
                    </label>
                    <label>
                        Document type
                        <select wire:model.live="doc_key">
                            <option value="">All types</option>
                            @foreach ($docTypes as $type)
                                <option value="{{ $type['key'] }}">{{ $type['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
            </div>

            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>CNIC</th>
                            <th>Document</th>
                            <th>Status</th>
                            <th class="master-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $row)
                            <tr wire:key="doc-desk-{{ $row['client_id'] }}-{{ $row['doc_key'] }}-{{ $row['status'] }}">
                                <td>{{ $row['client_name'] }}</td>
                                <td>{{ $row['cnic'] }}</td>
                                <td>{{ $row['label'] }}</td>
                                <td>
                                    @if ($row['status'] === 'uploaded')
                                        <span class="status status--ready">Uploaded</span>
                                        @if ($row['file_name'])
                                            <div class="muted" style="font-size:0.68rem;margin-top:4px;">{{ $row['file_name'] }}</div>
                                        @endif
                                    @elseif ($row['status'] === 'na')
                                        <span class="status">N/A</span>
                                    @else
                                        <span class="status status--interview">Missing</span>
                                    @endif
                                </td>
                                <td class="master-row-actions">
                                    @if ($row['can_view'])
                                        <a
                                            class="row-action row-action--edit"
                                            href="{{ route('clients.documents.show', [$row['client_id'], $row['doc_key']]) }}"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <span>View</span>
                                        </a>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">
                                    @if ($filter === 'missing')
                                        No missing documents for this filter.
                                    @elseif ($filter === 'na')
                                        No N/A documents yet.
                                    @else
                                        No uploaded documents yet.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($paginator)
                {{ $paginator->links() }}
            @endif
        </section>
    @endif
</div>
