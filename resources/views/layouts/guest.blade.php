@php
    $agencySettings = \App\Models\AgencySettings::instance();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $agencySettings->name }} - Login</title>

        <!-- Favicon -->
        @if($agencySettings->favicon)
            <link rel="icon" href="{{ Storage::url($agencySettings->favicon) }}">
            <link rel="apple-touch-icon" href="{{ $agencySettings->logo ? Storage::url($agencySettings->logo) : Storage::url($agencySettings->favicon) }}">
        @elseif($agencySettings->logo)
            <link rel="icon" type="image/png" href="{{ Storage::url($agencySettings->logo) }}">
            <link rel="apple-touch-icon" href="{{ Storage::url($agencySettings->logo) }}">
        @else
            <link rel="icon" type="image/svg+xml" href="{{ asset('img/80-30_Bildmarke_RGB_24.svg') }}">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center">
                    @if($agencySettings->logo)
                        <img src="{{ Storage::url($agencySettings->logo) }}" alt="{{ $agencySettings->name }}" class="h-16 w-auto mb-2">
                    @else
                        <img src="{{ asset('img/80-30_Bildmarke_RGB_24.svg') }}" alt="{{ $agencySettings->name }}" class="h-16 w-auto mb-2">
                    @endif
                    <span class="text-xl font-semibold text-gray-700">{{ $agencySettings->name }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-2 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            <p class="mt-6 text-sm text-gray-500">
                WordPress Wartungssystem
            </p>
        </div>
    </body>
</html>
