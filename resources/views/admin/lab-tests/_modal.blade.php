<div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none;">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="closeModal()"></div>
    <div x-show="modalOpen" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto" style="display:none;">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-2xl flex items-center justify-between z-10">
            <h3 x-text="modalTitle" class="text-lg font-bold text-gray-900"></h3>
            <button @click="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @if($errors->any())
        <div class="mx-6 mt-4 bg-rose-50 border border-rose-200 rounded-xl p-4">
            <p class="text-sm font-semibold text-rose-700 mb-1">Please fix the following errors:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                <li class="text-xs text-rose-600">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form :action="modalAction" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="_method" :value="modalMethod">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="s_no" class="block text-sm font-medium text-gray-700 mb-1.5">S.No</label>
                    <input type="number" id="s_no" name="s_no" x-model="current.s_no"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="nabl_type" class="block text-sm font-medium text-gray-700 mb-1.5">NABL Type <span class="text-rose-500">*</span></label>
                    <select id="nabl_type" name="nabl_type" x-model="current.nabl_type" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition bg-white">
                        <option value="NABL">NABL</option>
                        <option value="NON NABL">NON NABL</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="discipline" class="block text-sm font-medium text-gray-700 mb-1.5">Discipline / Group <span class="text-rose-500">*</span></label>
                    <input type="text" id="discipline" name="discipline" list="disciplineList" x-model="current.discipline" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    <datalist id="disciplineList">
                        @foreach($disciplines as $value)<option value="{{ $value }}"></option>@endforeach
                    </datalist>
                </div>
                <div class="sm:col-span-2">
                    <label for="material" class="block text-sm font-medium text-gray-700 mb-1.5">Material / Product</label>
                    <input type="text" id="material" name="material" list="materialList" x-model="current.material"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                    <datalist id="materialList">
                        @foreach($materials as $value)<option value="{{ $value }}"></option>@endforeach
                    </datalist>
                </div>
                <div>
                    <label for="sample_quantity" class="block text-sm font-medium text-gray-700 mb-1.5">Sample Qty</label>
                    <input type="text" id="sample_quantity" name="sample_quantity" x-model="current.sample_quantity"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div class="sm:col-span-3">
                    <label for="parameter" class="block text-sm font-medium text-gray-700 mb-1.5">Parameter <span class="text-rose-500">*</span></label>
                    <input type="text" id="parameter" name="parameter" x-model="current.parameter" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="method" class="block text-sm font-medium text-gray-700 mb-1.5">Method</label>
                    <input type="text" id="method" name="method" x-model="current.method"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="lead_time" class="block text-sm font-medium text-gray-700 mb-1.5">Lead Time</label>
                    <input type="text" id="lead_time" name="lead_time" x-model="current.lead_time"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="charges_per_sample" class="block text-sm font-medium text-gray-700 mb-1.5">Charges per Sample <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" id="charges_per_sample" name="charges_per_sample" x-model="current.charges_per_sample" required
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="gst_amount" class="block text-sm font-medium text-gray-700 mb-1.5">18% GST Amount</label>
                    <input type="number" step="0.01" min="0" id="gst_amount" name="gst_amount" x-model="current.gst_amount"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-1.5">Total Amount</label>
                    <input type="number" step="0.01" min="0" id="total_amount" name="total_amount" x-model="current.total_amount"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="protocol_no" class="block text-sm font-medium text-gray-700 mb-1.5">Protocol No</label>
                    <input type="text" id="protocol_no" name="protocol_no" x-model="current.protocol_no"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="nabl_range" class="block text-sm font-medium text-gray-700 mb-1.5">NABL Range</label>
                    <input type="text" id="nabl_range" name="nabl_range" x-model="current.nabl_range"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div>
                    <label for="limit_of_quantification" class="block text-sm font-medium text-gray-700 mb-1.5">LOQ</label>
                    <input type="text" id="limit_of_quantification" name="limit_of_quantification" x-model="current.limit_of_quantification"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition">
                </div>
                <div class="sm:col-span-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1.5">Remarks</label>
                    <textarea id="remarks" name="remarks" x-model="current.remarks" rows="1"
                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition resize-none"></textarea>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer pb-2.5">
                        <input type="checkbox" name="is_active" value="1" :checked="current.is_active"
                            class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="closeModal()"
                    class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-brand-500/25 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Lab Test
                </button>
            </div>
        </form>
    </div>
</div>
