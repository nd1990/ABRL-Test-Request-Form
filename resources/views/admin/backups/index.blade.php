@extends('admin.layouts.app')

@section('title', 'Backups')

@section('content')
<div class="space-y-6 max-w-5xl">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Backups</h1>
            <p class="text-sm text-gray-500 mt-1">Protect your data — create manual backups or let the system back up automatically.</p>
        </div>
        @if($currentAdmin?->hasPermission('backups.create'))
        <form method="POST" action="{{ route('admin.backups.store') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Backup Now
            </button>
        </form>
        @endif
    </div>

    @if($currentAdmin?->hasPermission('backups.create'))
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-1">Auto Backup</h2>
            <p class="text-xs text-gray-500 mb-4">Schedule automatic database backups. Old automatic backups beyond the keep count are pruned. Manual backups are never auto-deleted.</p>

            <form method="POST" action="{{ route('admin.backups.settings') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700 mb-1.5">Frequency</label>
                    <select id="frequency" name="frequency"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        <option value="off" @selected(old('frequency', $settings['frequency']) === 'off')>Off</option>
                        <option value="daily" @selected(old('frequency', $settings['frequency']) === 'daily')>Daily</option>
                        <option value="weekly" @selected(old('frequency', $settings['frequency']) === 'weekly')>Weekly (Monday)</option>
                        <option value="monthly" @selected(old('frequency', $settings['frequency']) === 'monthly')>Monthly (1st)</option>
                    </select>
                </div>
                <div>
                    <label for="time" class="block text-sm font-medium text-gray-700 mb-1.5">Time</label>
                    <input type="time" id="time" name="time" value="{{ old('time', $settings['time']) }}"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                </div>
                <div>
                    <label for="keep" class="block text-sm font-medium text-gray-700 mb-1.5">Keep Last (auto backups)</label>
                    <input type="number" id="keep" name="keep" min="1" max="365" value="{{ old('keep', $settings['keep']) }}"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                </div>

                @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                        <li class="text-xs text-rose-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                    Save Auto Backup Settings
                </button>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Saved Backups ({{ count($backups) }})</h2>
        </div>

        @if(count($backups) === 0)
        <div class="px-5 py-12 text-center">
            <p class="text-sm text-gray-400">No backups yet. Click "Backup Now" to create your first one.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Backup File</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Type</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-gray-600">Created</th>
                        <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Size</th>
                        <th class="text-right px-4 py-2.5 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900 font-mono text-xs">{{ $backup['filename'] }}</td>
                        <td class="px-4 py-3">
                            @if($backup['type'] === 'Auto')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Auto</span>
                            @elseif($backup['type'] === 'Pre-restore')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Pre-restore</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Manual</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $backup['created_at']->format('d M Y H:i:s') }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">{{ $backup['size_human'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
<a href="{{ route('admin.backups.download', $backup['filename']) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download
                                </a>
                                @if($currentAdmin?->hasPermission('backups.restore'))
                                <form method="POST" action="{{ route('admin.backups.restore', $backup['filename']) }}" class="inline"
                                      x-data onsubmit="return confirm('Restore from this backup? The current database will be OVERWRITTEN. A safety backup of the current state will be created first.')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"/><path d="M20.49 15a9 9 0 11-2.12-9.36L23 10"/></svg>
                                        Restore
                                    </button>
                                </form>
                                @endif
                                @if($currentAdmin?->hasPermission('backups.delete'))
                                <form method="POST" action="{{ route('admin.backups.destroy', $backup['filename']) }}" class="inline"
                                      x-data onsubmit="return confirm('Delete this backup? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded-lg hover:bg-rose-100 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection