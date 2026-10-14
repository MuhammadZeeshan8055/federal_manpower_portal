<div class="bio-experience">
    <p class="bio-experience__hint">Work history (up to 5 rows — shows on the print form)</p>

    <div class="bio-experience__head">
        <span>From - To</span>
        <span>Company</span>
        <span>Project</span>
        <span>Country</span>
        <span>Job title</span>
    </div>

    @foreach ($experiences as $index => $row)
        <div class="bio-experience__row" wire:key="experience-row-{{ $index }}">
            <input type="text" wire:model="experiences.{{ $index }}.from_to" placeholder="e.g. 2019 - 2021" aria-label="From to {{ $index + 1 }}">
            <input type="text" wire:model="experiences.{{ $index }}.company" placeholder="Company" aria-label="Company {{ $index + 1 }}">
            <input type="text" wire:model="experiences.{{ $index }}.project" placeholder="Project" aria-label="Project {{ $index + 1 }}">
            <input type="text" wire:model="experiences.{{ $index }}.country" placeholder="Country" aria-label="Country {{ $index + 1 }}">
            <input type="text" wire:model="experiences.{{ $index }}.job_title" placeholder="Job title" aria-label="Job title {{ $index + 1 }}">
        </div>
    @endforeach
</div>
