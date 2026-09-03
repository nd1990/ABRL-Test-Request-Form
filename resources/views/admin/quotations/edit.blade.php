@extends('admin.layouts.app')

@section('title', 'Edit ' . $quotation->quotation_number)

@section('content')
<div class="space-y-6 max-w-5xl" x-data="quotationEditor()">

    <div class="flex items-center gap-3">
        <a href="{{ route('admin.quotations.show', $quotation) }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit {{ $quotation->quotation_number }}</h1>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.quotations.update', $quotation) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Client Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="client_name" class="block text-xs font-medium text-gray-700 mb-1">Client Name *</label>
                        <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $quotation->client_name) }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none @error('client_name') border-rose-500 @enderror">
                        @error('client_name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="company_name" class="block text-xs font-medium text-gray-700 mb-1">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $quotation->company_name) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $quotation->email) }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none @error('email') border-rose-500 @enderror">
                        @error('email')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $quotation->phone) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $quotation->address) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="address_line2" class="block text-xs font-medium text-gray-700 mb-1">Address Line 2</label>
                        <input type="text" id="address_line2" name="address_line2" value="{{ old('address_line2', $quotation->address_line2) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="city" class="block text-xs font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $quotation->city) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="state" class="block text-xs font-medium text-gray-700 mb-1">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state', $quotation->state) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="postal_code" class="block text-xs font-medium text-gray-700 mb-1">Postal / Zip Code</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $quotation->postal_code) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="country" class="block text-xs font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country', $quotation->country) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="gst_number" class="block text-xs font-medium text-gray-700 mb-1">GST Number</label>
                        <input type="text" id="gst_number" name="gst_number" value="{{ old('gst_number', $quotation->gst_number) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="pan_number" class="block text-xs font-medium text-gray-700 mb-1">PAN Number</label>
                        <input type="text" id="pan_number" name="pan_number" value="{{ old('pan_number', $quotation->pan_number) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none uppercase">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="courier_address" class="block text-xs font-medium text-gray-700 mb-1">Courier Address</label>
                        <input type="text" id="courier_address" name="courier_address" value="{{ old('courier_address', $quotation->courier_address) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="courier_city" class="block text-xs font-medium text-gray-700 mb-1">Courier City</label>
                        <input type="text" id="courier_city" name="courier_city" value="{{ old('courier_city', $quotation->courier_city) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="courier_state" class="block text-xs font-medium text-gray-700 mb-1">Courier State</label>
                        <input type="text" id="courier_state" name="courier_state" value="{{ old('courier_state', $quotation->courier_state) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="courier_postal_code" class="block text-xs font-medium text-gray-700 mb-1">Courier Postal Code</label>
                        <input type="text" id="courier_postal_code" name="courier_postal_code" value="{{ old('courier_postal_code', $quotation->courier_postal_code) }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Quotation Details</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="quotation_date" class="block text-xs font-medium text-gray-700 mb-1">Quotation Date *</label>
                        <input type="date" id="quotation_date" name="quotation_date" value="{{ old('quotation_date', $quotation->quotation_date->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="valid_until" class="block text-xs font-medium text-gray-700 mb-1">Valid Until</label>
                        <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', $quotation->valid_until ? $quotation->valid_until->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                    </div>
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                            <option value="draft" {{ old('status', $quotation->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status', $quotation->status) === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="accepted" {{ old('status', $quotation->status) === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ old('status', $quotation->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="cancelled" {{ old('status', $quotation->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                @php
                    $labOnly = $quotation->items->isNotEmpty() && $quotation->items->every(fn($i) => empty($i->service_id));
                @endphp
                @if($labOnly)
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Selected Tests</h2>
                    <span class="text-xs text-gray-400">Tests are added from the public quotation form.</span>
                </div>
                <div class="space-y-3">
                    @foreach($quotation->items as $index => $item)
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="hidden" name="lab_tests[{{ $index }}][lab_test_id]" value="{{ $item->lab_test_id ?? $item->service_id }}">
                        <input type="hidden" name="lab_tests[{{ $index }}][notes]" value="">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $item->service_name_snapshot }}</p>
                            <p class="text-xs text-gray-500 whitespace-pre-line">{{ $item->description_snapshot }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <label class="text-xs text-gray-500">No. of samples</label>
                            <input type="number" min="1" name="lab_tests[{{ $index }}][no_of_samples]" value="{{ $item->quantity }}"
                                   class="w-20 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-center">
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Services</h2>
                    <div class="flex items-center gap-2">
                        <select x-model="serviceToAdd"
                                class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none bg-white">
                            <option value="">Select a service...</option>
                            <template x-for="s in services" :key="s.id">
                                <option :value="s.id" x-text="s.name + ' (' + s.category + ')'"></option>
                            </template>
                        </select>
                        <button type="button" @click="addService()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Add Service
                        </button>
                    </div>
                </div>

                <div class="space-y-3">
                    <template x-for="(item, idx) in selectedServices" :key="idx">
                        <div class="flex flex-col sm:flex-row sm:items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="hidden" :name="'services[' + idx + '][service_id]'" :value="item.service_id">
                            <input type="hidden" :name="'services[' + idx + '][quantity]'" :value="item.quantity">
                            <input type="hidden" :name="'services[' + idx + '][discount]'" :value="item.discount">
                            <input type="hidden" :name="'services[' + idx + '][notes]'" :value="item.notes">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.service_name"></p>
                                <p class="text-xs text-gray-500" x-text="item.category"></p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap shrink-0">
                                <div class="flex items-center gap-1">
                                    <label class="text-xs text-gray-500">Qty</label>
                                    <input type="number" min="1" step="1" x-model.number="item.quantity"
                                           class="w-16 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-center">
                                </div>
                                <div class="flex items-center gap-1">
                                    <label class="text-xs text-gray-500">Discount</label>
                                    <input type="number" min="0" step="0.01" x-model.number="item.discount"
                                           class="w-20 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none text-right">
                                </div>
                                <input type="text" x-model="item.notes" placeholder="Notes"
                                       class="w-32 px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none">
                                <button type="button" @click="removeService(idx)"
                                        class="p-1 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="selectedServices.length === 0" class="text-center py-8 text-sm text-gray-400">
                        No services added yet. Use the dropdown above to add services.
                    </div>
                </div>

                <div x-show="selectedServices.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-end">
                        <div class="w-72 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <span class="font-medium text-gray-900" x-text="currency + computeSubtotal().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Discount</span>
                                <span class="font-medium text-gray-900" x-text="'-' + currency + computeDiscount().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tax</span>
                                <span class="font-medium text-gray-900" x-text="currency + computeTax().toFixed(2)"></span>
                            </div>
                            <div class="flex justify-between text-base font-bold pt-2 border-t border-gray-200">
                                <span class="text-gray-900">Grand Total</span>
                                <span class="text-brand-600" x-text="currency + computeGrandTotal().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Options</h2>
                <div class="flex flex-wrap items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="regenerate_pdf" value="1"
                               class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Regenerate PDF</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="resend_email" value="1"
                               class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500">
                        <span class="text-sm text-gray-700">Resend Email</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.quotations.show', $quotation) }}"
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition">
                    Update Quotation
                </button>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
function quotationEditor() {
    return {
        services: @js($services->toArray()),
        serviceToAdd: '',
        selectedServices: @js(collect($quotation->items)->map(fn($item) => [
            'service_id' => $item->service_id,
            'service_name' => $item->service_name_snapshot,
            'category' => $services->firstWhere('id', $item->service_id)?->category ?? '',
            'quantity' => $item->quantity,
            'discount' => $item->discount_snapshot,
            'notes' => $item->notes ?? '',
        ])->values()->toArray()),
        currency: @js($currency),

        addService() {
            if (!this.serviceToAdd) return;
            const service = this.services.find(s => s.id == this.serviceToAdd);
            if (!service) return;
            if (this.selectedServices.some(s => s.service_id == service.id)) return;
            this.selectedServices.push({
                service_id: service.id,
                service_name: service.name,
                category: service.category,
                quantity: 1,
                discount: 0,
                notes: ''
            });
            this.serviceToAdd = '';
        },

        removeService(idx) {
            this.selectedServices.splice(idx, 1);
        },

        computeSubtotal() {
            return this.selectedServices.reduce((sum, item) => {
                const service = this.services.find(s => s.id == item.service_id);
                const price = service ? parseFloat(service.price) : 0;
                return sum + (price * item.quantity);
            }, 0);
        },

        computeDiscount() {
            return this.selectedServices.reduce((sum, item) => {
                return sum + parseFloat(item.discount || 0);
            }, 0);
        },

        computeTax() {
            return this.selectedServices.reduce((sum, item) => {
                const service = this.services.find(s => s.id == item.service_id);
                const price = service ? parseFloat(service.price) : 0;
                const taxPct = service ? parseFloat(service.tax_percentage) : 0;
                const lineTotal = (price * item.quantity) - parseFloat(item.discount || 0);
                return sum + (lineTotal * taxPct / 100);
            }, 0);
        },

        computeGrandTotal() {
            return this.computeSubtotal() - this.computeDiscount() + this.computeTax();
        }
    }
}
</script>
@endpush
