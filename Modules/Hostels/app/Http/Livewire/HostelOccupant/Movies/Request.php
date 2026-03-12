<?php

namespace Modules\Hostels\Http\Livewire\HostelOccupant\Movies;

use Livewire\Component;
use Modules\Hostels\Models\HostelMovieRequest;

class Request extends Component
{
    public string $title       = '';
    public string $description = '';
    public string $urgency     = 'normal';

    protected $rules = [
        'title'       => 'required|string|max:255',
        'description' => 'nullable|string|max:2000',
        'urgency'     => 'required|in:low,normal,urgent',
    ];

    protected $messages = [
        'title.required' => 'Please enter the movie title.',
        'urgency.in'     => 'Please select a valid urgency level.',
    ];

    public function submit()
    {
        $this->validate();

        $user = auth('hostel_occupant')->user();

        HostelMovieRequest::create([
            'hostel_occupant_id' => $user->hostel_occupant_id,
            'hostel_id'          => $user->hostelOccupant->hostel_id,
            'title'              => $this->title,
            'description'        => $this->description,
            'urgency'            => $this->urgency,
            'status'             => 'pending',
        ]);

        session()->flash('success', 'Movie request submitted! We will notify you when it is available.');

        return $this->redirect(route('hostel_occupant.movies.index'));
    }

    public function render()
    {
        return view('hostels::livewire.hostel-occupant.movies.request')
            ->layout('hostels::layouts.occupant');
    }
}
