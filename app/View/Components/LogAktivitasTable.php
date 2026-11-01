<?php

namespace App\View\Components;

use App\Models\Permohonan;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Activitylog\Models\Activity;

class LogAktivitasTable extends Component
{
    public $log_aktivitas;
    public $delays;

    /**
     * Create a new component instance.
     */
    public function __construct(Permohonan $permohonan)
    {
        // get log aktivitas where performed on the permohonan and eager load causer
        $this->log_aktivitas = Activity::where('subject_type', Permohonan::class)
            ->where('subject_id', $permohonan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // calculate delays between activities
        $this->delays = $this->calculateDelays($this->log_aktivitas);
    }

    /**
     * Calculate delays between activities.
     */
    protected function calculateDelays($activities)
    {
        $delays = [];
        for ($i = 0; $i < $activities->count() - 1; $i++) {
            $current = $activities[$i];
            $next = $activities[$i + 1];
            $delays[$current->id] = $current->created_at->diffForHumans($next->created_at, true);
        }
        return $delays;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.log-aktivitas-table', [
            'log_aktivitas' => $this->log_aktivitas,
            'delays' => $this->delays,
        ]);
    }
}
