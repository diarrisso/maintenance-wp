<x-app-layout>
    <x-slot name="header">Administration</x-slot>

    @if(session('success'))
        <div class="mb-4 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-4xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Logo Section -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Logos</h3>
                    <p class="text-sm text-gray-500">Logos für PDF-Berichte und Website</p>
                </div>

                <div class="p-6 grid grid-cols-3 gap-6">
                    <!-- Main Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Hauptlogo (farbig)
                        </label>
                        @if($settings->logo)
                            <div class="mb-3 p-4 bg-gray-100 rounded-lg inline-block">
                                <img src="{{ $settings->logo_url }}" alt="Logo" class="max-h-16">
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.settings.delete-logo') }}?type=logo"
                                   onclick="return confirm('Logo wirklich löschen?')"
                                   class="text-red-600 hover:text-red-800 text-sm">Löschen</a>
                            </div>
                        @endif
                        <input type="file" name="logo" accept="image/*"
                               class="w-full rounded-lg border border-gray-300 p-2">
                        <p class="mt-1 text-xs text-gray-500">PNG/JPG, max. 2MB. Wird auf der Login-Seite und in PDF verwendet.</p>
                    </div>

                    <!-- White Logo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Logo (weiß) für Sidebar
                        </label>
                        @if($settings->logo_white)
                            <div class="mb-3 p-4 bg-primary-600 rounded-lg inline-block">
                                <img src="{{ $settings->logo_white_url }}" alt="Logo White" class="max-h-16">
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.settings.delete-logo') }}?type=logo_white"
                                   onclick="return confirm('Logo wirklich löschen?')"
                                   class="text-red-600 hover:text-red-800 text-sm">Löschen</a>
                            </div>
                        @endif
                        <input type="file" name="logo_white" accept="image/*"
                               class="w-full rounded-lg border border-gray-300 p-2">
                        <p class="mt-1 text-xs text-gray-500">PNG transparent empfohlen. Für Sidebar und PDF-Header.</p>
                    </div>

                    <!-- Favicon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Favicon (Browser-Tab)
                        </label>
                        @if($settings->favicon)
                            <div class="mb-3 p-4 bg-gray-100 rounded-lg inline-block">
                                <img src="{{ $settings->favicon_url }}" alt="Favicon" class="h-8 w-8">
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('admin.settings.delete-logo') }}?type=favicon"
                                   onclick="return confirm('Favicon wirklich löschen?')"
                                   class="text-red-600 hover:text-red-800 text-sm">Löschen</a>
                            </div>
                        @endif
                        <input type="file" name="favicon" accept="image/*,.ico"
                               class="w-full rounded-lg border border-gray-300 p-2">
                        <p class="mt-1 text-xs text-gray-500">PNG/ICO, 32x32 oder 64x64 Pixel empfohlen.</p>
                    </div>
                </div>
            </div>

            <!-- Agency Info -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Agentur-Informationen</h3>
                    <p class="text-sm text-gray-500">Diese Daten erscheinen auf den PDF-Berichten</p>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Firmenname <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $settings->name) }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $settings->email) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', $settings->phone) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                        <input type="text" name="website" id="website" value="{{ old('website', $settings->website) }}"
                               placeholder="www.example.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Adresse</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="address_street" class="block text-sm font-medium text-gray-700 mb-1">Straße & Hausnummer</label>
                        <input type="text" name="address_street" id="address_street" value="{{ old('address_street', $settings->address_street) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="address_zip" class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                            <input type="text" name="address_zip" id="address_zip" value="{{ old('address_zip', $settings->address_zip) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label for="address_city" class="block text-sm font-medium text-gray-700 mb-1">Stadt</label>
                            <input type="text" name="address_city" id="address_city" value="{{ old('address_city', $settings->address_city) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                        <div>
                            <label for="address_country" class="block text-sm font-medium text-gray-700 mb-1">Land</label>
                            <input type="text" name="address_country" id="address_country" value="{{ old('address_country', $settings->address_country) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        </div>
                    </div>

                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 mb-1">USt-IdNr. / Steuernummer</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $settings->tax_id) }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <!-- PDF Settings -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">PDF-Einstellungen</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="footer_text" class="block text-sm font-medium text-gray-700 mb-1">Footer-Text (optional)</label>
                        <textarea name="footer_text" id="footer_text" rows="2"
                                  placeholder="Zusätzlicher Text im Footer des PDF-Berichts"
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ old('footer_text', $settings->footer_text) }}</textarea>
                    </div>

                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 mb-1">Primärfarbe</label>
                        <div class="flex items-center space-x-3">
                            <input type="color" name="primary_color" id="primary_color" value="{{ old('primary_color', $settings->primary_color) }}"
                                   class="h-10 w-20 rounded border-gray-300 cursor-pointer">
                            <input type="text" value="{{ old('primary_color', $settings->primary_color) }}" readonly
                                   class="w-24 rounded-lg border-gray-300 bg-gray-50 text-sm"
                                   id="primary_color_text">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Farbe für Header und Akzente im PDF</p>
                    </div>
                </div>
            </div>

            <!-- Preview -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Vorschau Footer</h3>
                </div>
                <div class="p-6">
                    <div class="border rounded-lg p-4 bg-gray-50 text-center text-sm text-gray-600">
                        <p><strong>{{ $settings->name }}</strong> - WordPress Wartung & Entwicklung</p>
                        @if($settings->full_address)
                            <p>{{ $settings->full_address }}</p>
                        @endif
                        <p>
                            @if($settings->email)E-Mail: {{ $settings->email }}@endif
                            @if($settings->email && $settings->website) | @endif
                            @if($settings->website)Web: {{ $settings->website }}@endif
                        </p>
                        @if($settings->tax_id)
                            <p>USt-IdNr.: {{ $settings->tax_id }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Einstellungen speichern
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('primary_color').addEventListener('input', function(e) {
            document.getElementById('primary_color_text').value = e.target.value;
        });
    </script>
</x-app-layout>
