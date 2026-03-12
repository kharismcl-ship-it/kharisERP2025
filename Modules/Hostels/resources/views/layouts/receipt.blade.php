<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Receipt</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="no-print bg-white border-b px-6 py-3 flex gap-3">
        <button onclick="window.print()" class="px-4 py-1.5 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
            Print Receipt
        </button>
        <button onclick="window.history.back()" class="px-4 py-1.5 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200">
            Back
        </button>
    </div>
    {{ $slot }}
    @livewireScripts
</body>
</html>
