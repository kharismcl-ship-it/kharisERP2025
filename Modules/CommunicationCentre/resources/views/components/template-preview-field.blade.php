@php
    $template = $getTemplate();
    $previewData = $getPreviewData();
    
    $previewSubject = '';
    $previewBody = '';
    
    if ($template) {
        $previewSubject = $renderTemplate($template->subject);
        $previewBody = $renderTemplate($template->body);
    }
@endphp

<div {{ $attributes->class(['p-4 bg-gray-50 rounded-lg border']) }}>
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Template Preview</h3>
    
    @if($template)
        <div class="space-y-4">
            @if($previewSubject)
                <div>
                    <h4 class="text-sm font-medium text-gray-600 mb-1">Subject:</h4>
                    <div class="p-3 bg-white border rounded text-gray-800">
                        {{ $previewSubject }}
                    </div>
                </div>
            @endif
            
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-1">Body:</h4>
                <div class="p-3 bg-white border rounded prose max-w-none">
                    {!! nl2br(e($previewBody)) !!}
                </div>
            </div>
            
            <div class="text-xs text-gray-500">
                <p><strong>Channel:</strong> {{ ucfirst($template->channel) }}</p>
                <p><strong>Template:</strong> {{ $template->name }} ({{ $template->code }})</p>
            </div>
        </div>
    @else
        <div class="text-center text-gray-500 py-8">
            <p>Select a template to see the preview</p>
        </div>
    @endif
</div>