<div>
    <h1 class="text-2xl font-bold mb-4">{{ $group->name }} - Messages</h1>

    <div class="bg-white shadow-md rounded-lg p-4">
        @foreach ($messages as $message)
            <div class="mb-4">
                <div class="flex items-center mb-1">
                    <p class="font-bold">{{ $message->sender->name }}</p>
                    <p class="text-gray-500 text-sm ml-2">{{ $message->sent_at->format('M d, H:i') }}</p>
                </div>
                <p>{{ $message->content }}</p>
                @if ($message->media_url)
                    <a href="{{ $message->media_url }}" target="_blank" class="text-blue-500">View Media</a>
                @endif
            </div>
        @endforeach
    </div>

    {{ $messages->links() }}
</div>
