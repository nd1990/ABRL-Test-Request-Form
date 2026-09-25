@php
    $moduleSpec = collect(config('permissions.modules'))->map(function ($m, $k) {
        return [
            'key' => $k,
            'label' => $m['label'],
            'perms' => collect($m['permissions'])->map(fn ($label, $key) => ['key' => $key, 'label' => $label])->values(),
        ];
    })->values()->all();
    $selected = $selected ?? [];
@endphp

<div x-show="role === 'user'" x-cloak>
    <div class="flex items-center justify-between gap-3 mb-3 flex-wrap">
        <div>
            <h2 class="text-base font-bold text-gray-900">User Permissions</h2>
            <p class="text-sm text-gray-500 mt-0.5">Enable a module, then tick the actions this user may perform.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="selectAll()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Select All
            </button>
            <button type="button" @click="clearAll()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear All
            </button>
        </div>
    </div>

    @if($errors->has('permissions'))
    <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm">{{ $errors->first('permissions') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <template x-for="mod in modules" :key="mod.key">
            <div class="border border-gray-200 rounded-2xl overflow-hidden" :class="isEnabled(mod) || hasAnySelected(mod) ? 'border-brand-300 ring-1 ring-brand-100' : ''">
                <div class="px-4 py-3 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" :checked="isEnabled(mod)" @change="toggleModule(mod, $event.target.checked)"
                                class="sr-only peer">
                            <div class="w-10 h-6 bg-gray-300 peer-checked:bg-brand-600 rounded-full peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </label>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 leading-tight" x-text="mod.label"></p>
                            <p class="text-[11px] text-gray-500 leading-tight">
                                <span x-text="moduleSelectedCount(mod)"></span> of <span x-text="mod.perms.length"></span> selected
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 space-y-2">
                    <template x-for="perm in mod.perms" :key="perm.key">
                        <label class="flex items-center gap-3 cursor-pointer text-sm transition"
                            :class="isEnabled(mod) ? 'text-gray-800' : 'text-gray-400 cursor-not-allowed'">
                            <input type="checkbox" name="permissions[]" :value="perm.key" x-model="selected" :disabled="!isEnabled(mod)"
                                class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500 disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-text="perm.label"></span>
                        </label>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <p class="text-xs text-gray-400 mt-3">Permissions are enforced on every action, not just the menus. Master Admin accounts are not shown here as they automatically have full access.</p>
</div>