<x-filament-panels::page>
    <div class="space-y-4">
        @if($activeSurveyId)
            @php $survey = collect($availableSurveys)->firstWhere('id', $activeSurveyId); @endphp
            @if($survey)
                <x-filament::card>
                    <h2 class="text-xl font-bold mb-1">{{ $survey['title'] }}</h2>
                    @if($survey['description'])
                        <p class="text-sm text-gray-500 mb-4">{{ $survey['description'] }}</p>
                    @endif
                    @if($survey['is_anonymous'])
                        <p class="text-xs text-success-600 mb-4 flex items-center gap-1">
                            <x-heroicon-o-eye-slash class="w-4 h-4"/> This survey is anonymous — your identity will not be recorded.
                        </p>
                    @endif

                    <div class="space-y-6">
                        @foreach($survey['questions'] as $question)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ $question['question'] }}
                                    @if($question['is_required']) <span class="text-danger-500">*</span> @endif
                                </label>

                                @if($question['question_type'] === 'rating')
                                    <div class="flex gap-2">
                                        @foreach(range(1, 5) as $star)
                                            <button type="button"
                                                wire:click="$set('answers.{{ $question['id'] }}', {{ $star }})"
                                                class="w-10 h-10 rounded-full border-2 text-sm font-semibold transition-colors
                                                    {{ ($answers[$question['id']] ?? null) == $star
                                                        ? 'border-primary-500 bg-primary-500 text-white'
                                                        : 'border-gray-300 hover:border-primary-400' }}">
                                                {{ $star }}
                                            </button>
                                        @endforeach
                                    </div>

                                @elseif($question['question_type'] === 'yes_no')
                                    <div class="flex gap-3">
                                        @foreach(['Yes', 'No'] as $opt)
                                            <button type="button"
                                                wire:click="$set('answers.{{ $question['id'] }}', '{{ $opt }}')"
                                                class="px-4 py-2 rounded border text-sm font-medium transition-colors
                                                    {{ ($answers[$question['id']] ?? '') === $opt
                                                        ? 'border-primary-500 bg-primary-500 text-white'
                                                        : 'border-gray-300 hover:border-primary-400' }}">
                                                {{ $opt }}
                                            </button>
                                        @endforeach
                                    </div>

                                @elseif($question['question_type'] === 'multiple_choice' && !empty($question['options']))
                                    <div class="space-y-2">
                                        @foreach($question['options'] as $opt)
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="radio"
                                                    wire:model="answers.{{ $question['id'] }}"
                                                    value="{{ $opt }}"
                                                    class="text-primary-600"/>
                                                <span class="text-sm">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @else {{-- text --}}
                                    <textarea
                                        wire:model.live="answers.{{ $question['id'] }}"
                                        rows="3"
                                        class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm"
                                        placeholder="Your response..."></textarea>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-3 mt-6">
                        <x-filament::button wire:click="submitSurvey" color="success">Submit</x-filament::button>
                        <x-filament::button wire:click="cancelSurvey" color="gray" outlined>Cancel</x-filament::button>
                    </div>
                </x-filament::card>
            @endif
        @else
            @if(empty($availableSurveys))
                <x-filament::card>
                    <p class="text-center text-gray-500 py-6">No active surveys available for you right now.</p>
                </x-filament::card>
            @else
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($availableSurveys as $survey)
                        <x-filament::card>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-start justify-between">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $survey['title'] }}</h3>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-info-100 text-info-700">
                                        {{ str($survey['survey_type'])->headline() }}
                                    </span>
                                </div>
                                @if($survey['description'])
                                    <p class="text-sm text-gray-500">{{ $survey['description'] }}</p>
                                @endif
                                <p class="text-xs text-gray-400">
                                    {{ count($survey['questions']) }} questions
                                    @if($survey['ends_at'])
                                        &bull; Closes {{ \Illuminate\Support\Carbon::parse($survey['ends_at'])->diffForHumans() }}
                                    @endif
                                    @if($survey['is_anonymous'])
                                        &bull; Anonymous
                                    @endif
                                </p>
                                <x-filament::button wire:click="openSurvey({{ $survey['id'] }})" size="sm" class="self-start">
                                    Take Survey
                                </x-filament::button>
                            </div>
                        </x-filament::card>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>