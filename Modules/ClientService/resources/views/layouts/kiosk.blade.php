<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="antialiased">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex,nofollow" />
    <title>{{ $title ?? ($companyName ?? 'Visitor Check-In') }}</title>

    @filamentStyles
    @livewireStyles

    <style>
        /* -------------------------------------------------------
           Kiosk overrides — ensure full-height, clean background
        ------------------------------------------------------- */
        html, body {
            height: 100%;
            background: #f1f5f9; /* slate-100 */
        }

        /* Remove Filament wizard card shadow in kiosk — form card handles it */
        .kiosk-form-wrap .fi-sc-wizard {
            box-shadow: none;
            background: transparent;
        }

        /* Make the wizard a bit more spacious for touch */
        .kiosk-form-wrap .fi-sc-wizard-footer {
            padding: 1.25rem 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 dark:bg-gray-900">

    {{ $slot }}

    @filamentScripts
    @livewireScripts
</body>
</html>
