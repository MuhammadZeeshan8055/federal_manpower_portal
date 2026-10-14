<?php

namespace App\Livewire;

use App\Support\ModuleStats;
use Livewire\Attributes\On;
use Livewire\Component;

class ModuleSummaryStats extends Component
{
    public string $moduleKey = '';

    public function mount($moduleKey = '')
    {
        $this->moduleKey = $moduleKey;
    }

    // Alpine calls this when you open a module, or after save/delete
    #[On('refresh-module-stats')]
    public function refresh($key = null)
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
