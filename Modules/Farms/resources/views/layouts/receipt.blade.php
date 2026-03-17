<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Order Receipt' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Figtree', sans-serif; background: #f9fafb; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .receipt-card { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="text-gray-800">
    {{ $slot }}
    @livewireScripts
</body>
</html>
