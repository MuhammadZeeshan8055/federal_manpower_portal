<?php

namespace App\Livewire;

use App\Support\ModuleStats;
use Livewire\Attributes\On;
use Livewire\Component;

class ModuleSummaryStats extends Component
{
    public string $moduleKey = '';

    #[On('refresh-module-stats')]
    public function refresh($key = null): void
    {
        if (is_string($key)) {
            $this->moduleKey = $key;
        }
    }

    public function render()
    {
        return view('livewire.module-summary-stats', [
            'cards' => ModuleStats::cardsFor($this->moduleKey),
        ]);
    }
}
