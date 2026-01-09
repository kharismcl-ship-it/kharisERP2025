<?php

namespace Modules\HR\Http\Livewire\PerformanceReviews;

use Livewire\Component;
use Modules\HR\Models\PerformanceReview;

class Show extends Component
{
    public PerformanceReview $review;

    public function mount(PerformanceReview $review)
    {
        $this->review = $review;
    }

    public function render()
    {
        return view('hr::livewire.performance-reviews.show')
            ->layout('hr::layouts.master');
    }
}
