<?php

namespace Modules\HR\Http\Livewire\PerformanceReviews;

use Livewire\Component;
use Modules\HR\Models\PerformanceReview;

class Index extends Component
{
    public function render()
    {
        $reviews = PerformanceReview::with(['employee', 'reviewer', 'company', 'performanceCycle'])
            ->latest()
            ->paginate(10);

        return view('hr::livewire.performance-reviews.index', compact('reviews'))
            ->layout('hr::layouts.master');
    }
}
