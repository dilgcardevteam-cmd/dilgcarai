<x-app-layout>
    <div class="space-y-8 py-2">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-blue-600 mb-2">DILG KNOWLEDGE ASSISTANT</p>
                <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
                <p class="text-gray-500 mt-2">Manage system configurations and platform settings.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="glass-panel border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800 rounded-2xl">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="glass-panel border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 rounded-2xl">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Left Column: System Settings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Settings Card -->
                <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">General Settings</h2>
                    <form method="POST" action="{{ route('settings.update') }}" class="space-y-5">
                        @csrf
                        @method('PATCH')
                        
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <x-input-label for="system_name">System Name</x-input-label>
                                <x-text-input id="system_name" name="system_name" value="{{ $settings['system_name'] }}" class="input-shell" />
                            </div>
                            <div>
                                <x-input-label for="organization">Organization</x-input-label>
                                <x-text-input id="organization" name="organization" value="{{ $settings['organization'] }}" class="input-shell" />
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <x-input-label for="timezone">Timezone</x-input-label>
                                <select id="timezone" name="timezone" class="input-shell">
                                    <option value="Asia/Manila" {{ $settings['timezone'] === 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila (PHT)</option>
                                    <option value="UTC">UTC</option>
                                </select>
                            </div>
                            <div>
                                <x-input-label for="language">Language</x-input-label>
                                <select id="language" name="language" class="input-shell">
                                    <option value="en" {{ $settings['language'] === 'English' ? 'selected' : '' }}>English</option>
                                    <option value="fil">Filipino</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="checkbox" id="maintenance_mode" name="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <x-input-label for="maintenance_mode" class="mb-0">Enable Maintenance Mode</x-input-label>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn-premium">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- Storage Usage Card -->
                <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Storage Usage</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Used Space</p>
                                <p class="text-xs text-gray-500">{{ $storageUsage['used'] }} GB of {{ $storageUsage['total'] }} GB</p>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ $storageUsage['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full" style="width: {{ $storageUsage['percentage'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: PSGC Import -->
            <div class="space-y-6">
                <!-- PSGC Statistics -->
                <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">PSGC Data</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl">
                            <div>
                                <p class="text-xs font-semibold text-gray-500">Regions</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($psgcCounts['regions']) }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-2xl text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-violet-50 to-purple-50 rounded-2xl">
                            <div>
                                <p class="text-xs font-semibold text-gray-500">Provinces</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($psgcCounts['provinces']) }}</p>
                            </div>
                            <div class="p-3 bg-violet-100 rounded-2xl text-violet-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-2xl">
                            <div>
                                <p class="text-xs font-semibold text-gray-500">Cities/Municipalities</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($psgcCounts['cities']) }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-2xl text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PSGC Import Card -->
                <div class="glass-panel bg-white p-6 border border-gray-100 rounded-3xl shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Import PSGC Data</h2>
                    <form method="POST" action="{{ route('settings.import-psgc') }}" enctype="multipart/form-data" x-data="{ selectedFileName: '', isImporting: false }" @submit.prevent="isImporting = true; $el.submit();" class="space-y-5">
                        @csrf

                        <div class="space-y-3">
                            <x-input-label for="import_mode">Import Mode</x-input-label>
                            <select id="import_mode" name="import_mode" class="input-shell">
                                <option value="upsert">Upsert (Update or Insert)</option>
                                <option value="insert_only">Insert Only</option>
                                <option value="update_only">Update Only</option>
                                <option value="refresh">Refresh (Truncate & Re-import)</option>
                            </select>
                        </div>

                        <div class="space-y-3">
                            <x-input-label>PSGC CSV File</x-input-label>
                            <label class="relative block border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center cursor-pointer hover:border-sky-300 hover:bg-sky-50/50 transition-all duration-200 group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-300">
                                        <template x-if="!isImporting">
                                            <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </template>
                                        <template x-if="isImporting">
                                            <svg class="w-6 h-6 text-sky-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m0 0H15"></path></svg>
                                        </template>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700" x-text="isImporting ? 'Processing data...' : (selectedFileName ? selectedFileName : 'Click to upload or drag and drop')"></p>
                                    <p class="text-xs text-gray-500 mt-1" x-show="!selectedFileName && !isImporting">CSV files only (Max. 10MB)</p>
                                    <p class="text-xs text-sky-600 mt-2 font-bold" x-show="selectedFileName && !isImporting">File selected successfully</p>
                                </div>
                                <input id="psgc_csv" name="psgc_csv" type="file" class="hidden" accept=".csv" :disabled="isImporting" @change="selectedFileName = $event.target.files[0].name" />
                            </label>
                        </div>

                        <div class="flex flex-col gap-4">
                            <button type="submit" x-show="selectedFileName && !isImporting" class="btn-premium w-full py-4 text-sm font-bold flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import PSGC Data
                            </button>
                            <button type="button" x-show="isImporting" disabled class="btn-premium w-full py-4 text-sm font-bold flex items-center justify-center gap-2 opacity-70 cursor-not-allowed">
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m0 0H15"></path></svg>
                                Importing Data...
                            </button>
                            <div class="text-center">
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Supports Standard PSGC CSV Format</p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
