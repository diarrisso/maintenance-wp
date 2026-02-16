@php
    $agencySettings = \App\Models\AgencySettings::instance();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $agencySettings->name }} - Wartung</title>

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

        <!-- Dynamic Primary Color -->
        <style>
            :root {
                --primary-color: {{ $agencySettings->primary_color ?? '#2563eb' }};
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Laravel Notify -->
        @notifyCss
    </head>
    <body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
        <div class="flex h-screen overflow-hidden">

            <!-- Sidebar -->
            <aside
                class="fixed inset-y-0 left-0 z-50 w-64 bg-primary-600 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="flex flex-col h-full">
                    <!-- Logo -->
                    <div class="flex items-center justify-between h-16 px-6 bg-primary-700">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                            @if($agencySettings->logo_white)
                                <img src="{{ Storage::url($agencySettings->logo_white) }}" alt="{{ $agencySettings->name }}" class="h-8 w-auto">
                            @elseif($agencySettings->logo)
                                <img src="{{ Storage::url($agencySettings->logo) }}" alt="{{ $agencySettings->name }}" class="h-8 w-auto">
                            @else
                                <img src="{{ asset('img/80-30_Bildmarke_RGB_24.svg') }}" alt="{{ $agencySettings->name }}" class="h-8 w-auto">
                            @endif
                            <span class="text-xl font-bold text-white truncate">{{ $agencySettings->name }}</span>
                        </a>
                        <button @click="sidebarOpen = false" class="lg:hidden text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Dashboard
                        </a>

                        @if(Auth::user()->isDeveloper())
                        <a href="{{ route('clients.index') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('clients.*') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Kunden
                        </a>

                        <a href="{{ route('websites.index') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('websites.*') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            Websites
                        </a>
                        @endif

                        <a href="{{ route('reports.index') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('reports.*') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Berichte
                        </a>

                        <a href="{{ route('reports.archive') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('reports.archive') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            Archiv
                        </a>

                        @if(Auth::user()->isDeveloper())
                        <a href="{{ route('entwicklers.index') }}"
                           class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('entwicklers.*') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            Entwickler
                        </a>

                        <div class="mt-6 pt-6 border-t border-primary-500">
                            <p class="px-4 text-xs font-semibold text-primary-200 uppercase tracking-wider mb-2">Administration</p>
                            <a href="{{ route('admin.settings') }}"
                               class="flex items-center px-4 py-3 text-white rounded-lg transition-colors {{ request()->routeIs('admin.*') ? 'bg-primary-700' : 'hover:bg-primary-500' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Einstellungen
                            </a>
                        </div>
                        @endif
                    </nav>

                    <!-- User Menu -->
                    <div class="px-4 py-4 border-t border-primary-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-primary-400 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs text-primary-200 hover:text-white">
                                        Abmelden
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Overlay für mobile -->
            <div
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            ></div>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-hidden">
                <!-- Top Bar -->
                <header class="bg-white shadow-sm z-10">
                    <div class="flex items-center justify-between px-6 py-4">
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        @isset($header)
                            <div class="flex-1">
                                <h2 class="text-2xl font-semibold text-gray-800">{{ $header }}</h2>
                            </div>
                        @endisset
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto bg-gray-50">
                    <div class="px-6 py-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <div x-data="confirmModal()"
             x-cloak
             @open-confirm-modal.window="open = true; title = $event.detail.title; message = $event.detail.message; formToSubmit = $event.detail.form; actionType = $event.detail.type || 'delete'; buttonText = $event.detail.buttonText || 'Bestätigen'">
            <div x-show="open" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <div x-show="open"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                         @click="cancel()"></div>

                    <div x-show="open"
                         x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom bg-white rounded-lg shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                 :class="actionType === 'delete' ? 'bg-red-100' : 'bg-blue-100'">
                                <template x-if="actionType === 'delete'">
                                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </template>
                                <template x-if="actionType !== 'delete'">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                                    </svg>
                                </template>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900" x-text="title"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500" x-text="message"></p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                            <button type="button" @click="confirmAction()"
                                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white border border-transparent rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto sm:text-sm"
                                    :class="actionType === 'delete' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500'"
                                    x-text="buttonText">
                            </button>
                            <button type="button" @click="cancel()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm">
                                Abbrechen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function confirmModal() {
                return {
                    open: false,
                    title: '',
                    message: '',
                    formToSubmit: null,
                    actionType: 'delete',
                    buttonText: 'Bestätigen',
                    confirmAction() {
                        if (this.formToSubmit) {
                            this.formToSubmit.submit();
                        }
                        this.open = false;
                    },
                    cancel() {
                        this.open = false;
                        this.formToSubmit = null;
                    }
                }
            }

            window.confirmDelete = function(form, title, message) {
                window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                    detail: { title, message, form, type: 'delete', buttonText: 'Löschen' }
                }));
            }

            window.confirmAction = function(form, title, message, buttonText) {
                window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                    detail: { title, message, form, type: 'confirm', buttonText: buttonText || 'Bestätigen' }
                }));
            }
        </script>

        <x-notify::notify />
        @notifyJs
    </body>
</html>
