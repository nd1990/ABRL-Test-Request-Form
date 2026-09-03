<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ABRL Test Request Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .step-enter { animation: fadeUp .35s ease both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .spinner { width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.35); border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; display: inline-block; vertical-align: middle; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        ::selection { background: #c7d2fe; }
        body { background: #f6f7fb; }
        select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .75rem center; background-size: 1em; padding-right: 2.5rem !important; appearance: none; -webkit-appearance: none; }
    </style>
</head>
<body class="font-sans text-gray-800 antialiased min-h-screen">

<div x-data="quotationApp()" x-init="initApp()" @input="saveState()" @change="saveState()" class="min-h-screen flex flex-col">

    <!-- ======= HEADER ======= -->
    <header class="bg-white/80 backdrop-blur border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" /></svg>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 leading-tight">ABRL Test Request Form</h1>
                    <p class="text-xs text-gray-500">Request a Quotation</p>
                </div>
            </div>
            <button type="button" @click="resetForm()" x-show="hasSavedState()" x-cloak
                class="text-xs font-medium text-gray-500 hover:text-rose-600 border border-gray-200 hover:border-rose-200 rounded-lg px-3 py-1.5 transition">
                Start Over
            </button>
        </div>
    </header>

    <!-- ======= STEP INDICATOR ======= -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4">
            <div class="flex items-center justify-between overflow-x-auto scrollbar-hide" x-cloak>
                <template x-for="(s, i) in steps" :key="i">
                    <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0" :class="i > 0 ? 'sm:pl-2' : ''">
                        <div class="hidden sm:flex h-px w-6 lg:w-10 bg-gray-200" x-show="i > 0"></div>
                        <button type="button"
                            @click="gotoStep(i)"
                            :class="step === i + 1 ? 'text-gray-900' : (step > i + 1 ? 'text-indigo-600' : 'text-gray-400')"
                            class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm font-medium py-1 px-0.5 cursor-pointer">
                            <span
                                :class="step === i + 1 ? 'bg-indigo-600 text-white ring-4 ring-indigo-100' : (step > i + 1 ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-500')"
                                class="w-6 h-6 sm:w-7 sm:h-7 rounded-full flex items-center justify-center text-[11px] sm:text-xs font-bold shrink-0 transition">
                                <span x-show="step > i + 1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6L9 17l-5-5" /></svg>
                                </span>
                                <span x-show="step <= i + 1" x-text="i + 1"></span>
                            </span>
                            <span class="whitespace-nowrap hidden sm:inline" x-text="s"></span>
                        </button>
                    </div>
</template>
                    </div>
                </div>
    </div>

    <main class="flex-1">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">

            <!-- ======= GLOBAL ERROR ======= -->
            <div x-show="serverError" x-cloak x-transition
                class="mb-6 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl px-4 py-3 text-sm flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                <div>
                    <p class="font-semibold" x-text="serverError"></p>
                </div>
            </div>

            <!-- ================= STEP 1: PARTY DETAILS ================= -->
            <section x-show="step === 1" x-cloak class="step-enter">
                <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">User Details</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Enter your company and contact information.</p>
                        </div>
                    </div>
                    <div class="space-y-5">
                        <!-- Company Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Company Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="company_name" x-model="form.company_name" placeholder="Enter company name"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="shouldShowValidation(1) && !form.company_name.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                            <p x-show="shouldShowValidation(1) && !form.company_name.trim()" class="text-xs text-rose-500 mt-1">Please enter company name</p>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Complete Address <span class="text-gray-400">(for Report &amp; Invoice)</span> <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <div class="sm:col-span-3">
                                    <input type="text" x-model="form.address" placeholder="Street Address"
                                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        :class="shouldShowValidation(1) && !form.address.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                </div>
                                <div class="sm:col-span-3">
                                    <input type="text" x-model="form.address_line2" placeholder="Street Address Line 2"
                                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                </div>
                                <div>
                                    <select x-model="form.city" @change="onCityChange()"
                                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white"
                                        :class="shouldShowValidation(1) && !form.city ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                        <option value="">Select City</option>
                                        <template x-for="city in cityOptions()" :key="city">
                                            <option x-bind:value="city" x-text="city"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <select x-model="form.state" :disabled="true"
                                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-100 disabled:cursor-not-allowed disabled:opacity-70">
                                        <template x-if="form.state">
                                            <option :value="form.state" x-text="form.state"></option>
                                        </template>
                                        <template x-if="!form.state">
                                            <option value="">Select City first</option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <input type="text" x-model="form.postal_code" placeholder="Postal / Zip Code"
                                        class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        :class="shouldShowValidation(1) && !form.postal_code.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                </div>
                            </div>
                        </div>

                        <!-- Different Courier Address -->
                        <div>
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox" x-model="form.different_courier_address"
                                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <span class="text-sm text-gray-700">Provide a different courier address for report &amp; invoice delivery</span>
                            </label>
                            <div x-show="form.different_courier_address" x-cloak class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Courier Address <span class="text-gray-400">(for Report &amp; Invoice)</span> <span class="text-rose-500">*</span></label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <div class="sm:col-span-3">
                                        <input type="text" x-model="form.courier_address" placeholder="Street Address"
                                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                            :class="shouldShowValidation(1) && form.different_courier_address && !form.courier_address.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <input type="text" x-model="form.courier_address_line2" placeholder="Street Address Line 2"
                                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </div>
                                    <div>
                                        <select x-model="form.courier_city" @change="onCourierCityChange()"
                                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white"
                                            :class="shouldShowValidation(1) && form.different_courier_address && !form.courier_city ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                            <option value="">Select City</option>
                                            <template x-for="city in cityOptions()" :key="city">
                                                <option x-bind:value="city" x-text="city"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <select x-model="form.courier_state" :disabled="true"
                                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-gray-100 disabled:cursor-not-allowed disabled:opacity-70">
                                            <template x-if="form.courier_state">
                                                <option :value="form.courier_state" x-text="form.courier_state"></option>
                                            </template>
                                            <template x-if="!form.courier_state">
                                                <option value="">Select City first</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" x-model="form.courier_postal_code" placeholder="Postal / Zip Code"
                                            class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                            :class="shouldShowValidation(1) && form.different_courier_address && !form.courier_postal_code.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Person & Mobile -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Contact Person Name <span class="text-rose-500">*</span></label>
                                <input type="text" id="client_name" x-model="form.client_name" placeholder="Enter contact person name"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    :class="shouldShowValidation(1) && !form.client_name.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                <p x-show="shouldShowValidation(1) && !form.client_name.trim()" class="text-xs text-rose-500 mt-1">Please enter contact person name</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mobile Number <span class="text-gray-400">(10-digit Indian)</span> <span class="text-rose-500">*</span></label>
                                <input type="tel" id="mobile" x-model="form.mobile" placeholder="Enter 10-digit mobile number" maxlength="10"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    :class="shouldShowValidation(1) && (form.mobile.trim() && !/^[6-9]\d{9}$/.test(form.mobile.trim())) ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                <p x-show="shouldShowValidation(1) && !form.mobile.trim()" class="text-xs text-rose-500 mt-1">Please enter mobile number</p>
                                <p x-show="shouldShowValidation(1) && form.mobile.trim() && !/^[6-9]\d{9}$/.test(form.mobile.trim())" class="text-xs text-rose-500 mt-1">Please enter a valid 10-digit Indian mobile number</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email <span class="text-rose-500">*</span></label>
                            <input type="email" id="email" x-model="form.email" placeholder="Enter email address"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="shouldShowValidation(1) && (!form.email.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                            <p x-show="shouldShowValidation(1) && !form.email.trim()" class="text-xs text-rose-500 mt-1">Please enter email address</p>
                            <p x-show="shouldShowValidation(1) && form.email.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)" class="text-xs text-rose-500 mt-1">Please enter a valid email address</p>
                        </div>

                        <!-- GST & PAN -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">GST Number <span class="text-rose-500">*</span></label>
                                <input type="text" id="gst_number" x-model="form.gst_number" placeholder="Enter 15-character GSTIN" maxlength="15"
                                    @input="form.gst_number = form.gst_number.toUpperCase()"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition uppercase"
                                    :class="shouldShowValidation(1) && (!form.gst_number.trim() || !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][0-9A-Z]Z[0-9A-Z]$/.test(form.gst_number.trim())) ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                <p x-show="shouldShowValidation(1) && !form.gst_number.trim()" class="text-xs text-rose-500 mt-1">Please enter GST number</p>
                                <p x-show="shouldShowValidation(1) && form.gst_number.trim() && !/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][0-9A-Z]Z[0-9A-Z]$/.test(form.gst_number.trim())" class="text-xs text-rose-500 mt-1">Please enter a valid 15-character GSTIN</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">PAN Number <span class="text-rose-500">*</span></label>
                                <input type="text" id="pan_number" x-model="form.pan_number" placeholder="Enter 10-character PAN" maxlength="10"
                                    @input="form.pan_number = form.pan_number.toUpperCase()"
                                    class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition uppercase"
                                    :class="shouldShowValidation(1) && (!form.pan_number.trim() || !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(form.pan_number.trim())) ? 'border-rose-400 ring-1 ring-rose-200' : ''">
                                <p x-show="shouldShowValidation(1) && !form.pan_number.trim()" class="text-xs text-rose-500 mt-1">Please enter PAN number</p>
                                <p x-show="shouldShowValidation(1) && form.pan_number.trim() && !/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(form.pan_number.trim())" class="text-xs text-rose-500 mt-1">Please enter a valid 10-character PAN</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="validateStep(1)"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/25 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Continue to Sample Details
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14" /><path d="M12 5l7 7-7 7" /></svg>
                    </button>
                </div>
                </div>
            </section>

            <!-- ================= STEP 2: SAMPLE DETAILS ================= -->
            <section x-show="step === 2" x-cloak class="step-enter">
                <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z" /></svg>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Sample Details</h2>
                            <p class="text-sm text-gray-500 mt-0.5">Provide details for each sample.</p>
                        </div>
                    </div>
                    <div class="mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                            <p class="text-sm font-medium text-amber-800">It is required to fill out the test report form separately for each sample.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name of Sample <span class="text-rose-500">*</span></label>
                            <input type="text" id="sample_name" x-model="form.sample_name" placeholder="e.g. Bio-stimulant Sample"
                                :class="shouldShowValidation(2) && !form.sample_name.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <p x-show="shouldShowValidation(2) && !form.sample_name.trim()" class="text-xs text-rose-500 mt-1">Please enter sample name</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sample Batch No. <span class="text-rose-500">*</span></label>
                            <input type="text" id="sample_batch_no" x-model="form.sample_batch_no" placeholder="e.g. BS-2024-001"
                                :class="shouldShowValidation(2) && !form.sample_batch_no.trim() ? 'border-rose-400 ring-1 ring-rose-200' : ''"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <p x-show="shouldShowValidation(2) && !form.sample_batch_no.trim()" class="text-xs text-rose-500 mt-1">Please enter sample batch no.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Sample Physical Form <span class="text-rose-500">*</span></label>
                            <select id="sample_physical_form" x-model="form.sample_physical_form"
                                :class="shouldShowValidation(2) && !form.sample_physical_form ? 'border-rose-400 ring-1 ring-rose-200' : ''"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                                <option value="">Select</option>
                                <option>Solid</option>
                                <option>Powder</option>
                                <option>Liquid</option>
                                <option>Semi-Liquid</option>
                                <option>Paste</option>
                                <option>Other</option>
                            </select>
                            <p x-show="shouldShowValidation(2) && !form.sample_physical_form" class="text-xs text-rose-500 mt-1">Please select sample physical form</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Specific Storage Condition <span class="text-rose-500">*</span></label>
                            <select id="sample_storage_condition" x-model="form.sample_storage_condition"
                                :class="shouldShowValidation(2) && !form.sample_storage_condition ? 'border-rose-400 ring-1 ring-rose-200' : ''"
                                class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                                <option value="">Select</option>
                                <option>Cool & Dry Place at Room Temperature (RT)</option>
                                <option>Refrigerated</option>
                                <option>Frozen</option>
                                <option>Room Temperature</option>
                                <option>Other</option>
                            </select>
                            <p x-show="shouldShowValidation(2) && !form.sample_storage_condition" class="text-xs text-rose-500 mt-1">Please select storage condition</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between">
                    <button type="button" @click="step = 1; saveState(); window.scrollTo({top:0, behavior:'smooth'})"
                        class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 font-medium text-sm px-4 py-2.5 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5" /><path d="M12 19l-7-7 7-7" /></svg>
                        Back
                    </button>
                    <button type="button" @click="validateStep(2)"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/25 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Continue to Select Tests
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14" /><path d="M12 5l7 7-7 7" /></svg>
                    </button>
                </div>
                </div>
            </section>

            <!-- ================= STEP 3: SELECT TESTS ================= -->
            <section x-show="step === 3" x-cloak class="step-enter">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900">Select Tests</h2>
                        <p class="text-sm text-gray-500 mt-0.5" x-text="'Choose the tests you need. (' + selectedTests.length + '/' + maxTests + ' selected)'"></p>
                    </div>
                    <span x-show="selectedTests.length > 0" class="text-sm font-semibold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full" x-text="selectedTests.length + ' test(s) selected'"></span>
                </div>

                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12.01" y2="16" /><path d="M12 8v4" /></svg>
                        <p class="text-sm font-medium text-blue-800">Maximum <span class="font-bold" x-text="maxTests"></span> tests allowed per quotation. If you need more, please submit this form and fill out a new one.</p>
                    </div>
                </div>

                <div x-show="shouldShowValidation(3) && selectedTests.length === 0" class="mb-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-lg px-3 py-2.5 text-sm flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full shrink-0"></span>Please select at least one test.
                </div>

                <div class="space-y-4">
                    <template x-if="activeSlot()">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-3 mb-4">
                                <span class="text-sm font-bold text-gray-800" x-text="'NABL Testing Selection ' + activeSlot().index"></span>
                                <span x-show="activeSlot().parameter" class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full" x-text="activeSlot().parameter ? 'Selected: ' + activeSlot().parameter.parameter : ''"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">NABL / NON NABL</label>
                                    <select x-model="activeSlot().nabl" @change="onSlotNabl(currentSlotIndex)"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white">
                                        <option value="">Select</option>
                                        <template x-for="t in nablOptions" :key="t"><option :value="t" x-text="t"></option></template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Discipline</label>
                                    <select x-model="activeSlot().discipline" @change="onSlotDiscipline(currentSlotIndex)" :disabled="!activeSlot().nabl"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white disabled:bg-gray-50 disabled:text-gray-400">
                                        <option value="">Select</option>
                                        <template x-for="d in activeSlot().disciplineOptions" :key="d"><option :value="d" x-text="d"></option></template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Material / Product</label>
                                    <select x-model="activeSlot().material" @change="onSlotMaterial(currentSlotIndex)" :disabled="!activeSlot().discipline"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white disabled:bg-gray-50 disabled:text-gray-400">
                                        <option value="">Select</option>
                                        <template x-for="m in activeSlot().materialOptions" :key="m"><option :value="m" x-text="m"></option></template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Parameter / Test</label>
                                    <select @change="onSlotParameter(currentSlotIndex, $event)" :disabled="!activeSlot().material || activeSlot().parameterOptions.length === 0"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition bg-white disabled:bg-gray-50 disabled:text-gray-400">
                                        <option value="">Select</option>
                                        <template x-for="p in activeSlot().parameterOptions" :key="p.id"><option :value="p.id" x-text="p.parameter"></option></template>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-3">
                                <span class="text-xs text-gray-400" x-text="activeSlot().loading ? 'Loading options...' : ''"></span>
                                <button type="button" @click="addFromSlot(currentSlotIndex)" :disabled="!activeSlot().parameter"
                                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2 rounded-lg shadow transition disabled:opacity-40 disabled:cursor-not-allowed">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14" /><path d="M5 12h14" /></svg>
                                    Add to Quotation
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Selected tests summary inside step 3 -->
                <div x-show="selectedTests.length > 0" x-cloak class="mt-5 bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                    <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-3">Selected Tests</p>
                    <template x-for="(t, idx) in selectedTests" :key="'sel-' + t.id + '-' + idx">
                        <div class="flex items-start justify-between gap-3 py-2 border-b border-gray-100 last:border-0">
                            <div class="min-w-0 flex items-start gap-2.5">
                                <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[11px] font-bold shrink-0 mt-0.5" x-text="idx + 1"></span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800" x-text="t.parameter"></p>
                                    <p class="text-xs text-gray-500" x-text="t.discipline + (t.material ? ' — ' + t.material : '')"></p>
                                </div>
                            </div>
                            <button type="button" @click="removeTest(idx)" class="text-gray-400 hover:text-rose-500 transition shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18" /><path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2" /><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6" /></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div x-show="selectedTests.length === 0" class="bg-amber-50 border border-amber-200 rounded-xl p-5 text-sm text-amber-700 mt-5">
                    No tests selected yet. Choose your tests from the NABL Testing Selection dropdowns above, then click "Add to Quotation".
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" @click="step = 2; saveState(); window.scrollTo({top:0, behavior:'smooth'})"
                        class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 font-medium text-sm px-4 py-2.5 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5" /><path d="M12 19l-7-7 7-7" /></svg>
                        Back
                    </button>
                    <button type="button" @click="validateStep(3)"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-lg shadow-indigo-500/25 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Continue to Review
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14" /><path d="M12 5l7 7-7 7" /></svg>
                    </button>
                </div>
            </section>

            <!-- ================= STEP 4: REVIEW ================= -->
            <section x-show="step === 4" x-cloak class="step-enter">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-gray-900">Preview Your Quotation</h2>
                                <p class="text-sm text-gray-500 mt-0.5">Review the details below, then accept the terms to download and send.</p>
                            </div>
                            <button type="button" @click="previewMode = !previewMode"
                                class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold px-4 py-2 rounded-xl border transition"
                                :class="previewMode ? 'bg-indigo-600 text-white border-indigo-600 hover:bg-indigo-700' : 'bg-white text-indigo-600 border-indigo-200 hover:bg-indigo-50'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" /><circle cx="12" cy="12" r="3" /></svg>
                                <span x-text="previewMode ? 'Hide Preview' : 'Preview'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- User Details -->
                        <div class="rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-50/60 via-white to-white shadow-sm overflow-hidden">
                            <div class="px-4 sm:px-5 py-3.5 flex items-center gap-3 bg-gradient-to-r from-indigo-600 to-violet-600">
                                <span class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" /><circle cx="12" cy="7" r="4" /></svg>
                                </span>
                                <h3 class="text-sm sm:text-base font-bold text-white tracking-wide">User Details</h3>
                                <span class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-100 bg-emerald-500/25 rounded-full px-2.5 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12" /></svg>
                                    Filled
                                </span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Company Name</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.company_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Contact Person</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.client_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Mobile</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.mobile ? ('+91 ' + form.mobile) : '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Email</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.email || '—'"></p>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Address</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="[form.address, form.address_line2, form.city, form.state, form.postal_code].filter(Boolean).join(', ') || '—'"></p>
                                    </div>
                                    <div class="sm:col-span-2" x-show="form.different_courier_address">
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">Courier Address</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="[form.courier_address, form.courier_address_line2, form.courier_city, form.courier_state, form.courier_postal_code].filter(Boolean).join(', ') || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">GST Number</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.gst_number || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-indigo-400 font-semibold">PAN Number</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.pan_number || '—'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sample Details -->
                        <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50/60 via-white to-white shadow-sm overflow-hidden">
                            <div class="px-4 sm:px-5 py-3.5 flex items-center gap-3 bg-gradient-to-r from-emerald-600 to-teal-600">
                                <span class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 100 20 10 10 0 000-20z" /><path d="M12 6v6l4 2" /></svg>
                                </span>
                                <h3 class="text-sm sm:text-base font-bold text-white tracking-wide">Sample Details</h3>
                                <span class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-semibold text-emerald-100 bg-emerald-500/25 rounded-full px-2.5 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12" /></svg>
                                    Filled
                                </span>
                            </div>
                            <div class="p-4 sm:p-5">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-5 gap-y-4">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-emerald-500 font-semibold">Sample Name</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.sample_name || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-emerald-500 font-semibold">Batch No.</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.sample_batch_no || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-emerald-500 font-semibold">Physical Form</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.sample_physical_form || '—'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-emerald-500 font-semibold">Storage</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-0.5" x-text="form.sample_storage_condition || '—'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Tests Table -->
                        <div class="rounded-2xl border border-violet-100 bg-white shadow-sm overflow-hidden">
                            <div class="px-4 sm:px-5 py-3.5 flex items-center gap-3 bg-gradient-to-r from-violet-600 to-fuchsia-600">
                                <span class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4" /><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" /></svg>
                                </span>
                                <h3 class="text-sm sm:text-base font-bold text-white tracking-wide">Selected Tests</h3>
                                <span class="ml-auto inline-flex items-center gap-1.5 text-[11px] font-semibold text-violet-100 bg-violet-500/25 rounded-full px-2.5 py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12" /></svg>
                                    <span x-text="selectedTests.length + ' Test' + (selectedTests.length === 1 ? '' : 's')"></span>
                                </span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100 bg-gray-50/60">
                                            <th class="py-3 px-4 sm:px-5 font-semibold">Test / Parameter</th>
                                            <th class="py-3 px-3 font-semibold">Method</th>
                                            <th class="py-3 px-4 sm:px-5 font-semibold text-right">No. of Samples</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(t, idx) in selectedTests" :key="'rev-' + t.id + '-' + idx">
                                            <tr class="border-b border-gray-100 align-top hover:bg-violet-50/40 transition">
                                                <td class="py-3 px-4 sm:px-5 pr-3">
                                                    <p class="font-semibold text-gray-900" x-text="t.parameter"></p>
                                                    <p class="text-xs text-gray-500 mt-0.5" x-text="[t.discipline, t.material].filter(Boolean).join(' — ')"></p>
                                                    <p class="text-xs text-gray-400" x-text="t.protocol_no ? 'Protocol: ' + t.protocol_no : ''"></p>
                                                </td>
                                                <td class="py-3 px-3 text-xs text-gray-600" x-text="t.method || '—'"></td>
                                                <td class="py-3 px-4 sm:px-5 text-right">
                                                    <span class="inline-flex items-center gap-1 font-bold text-violet-700 bg-violet-50 rounded-full px-2.5 py-0.5" x-text="t.no_of_samples"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="!selectedTests.length">
                                            <td class="py-6 text-center text-sm text-gray-400" colspan="3">No tests selected.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div x-show="form.notes" class="rounded-2xl border border-amber-100 bg-gradient-to-br from-amber-50/60 via-white to-white shadow-sm overflow-hidden">
                            <div class="px-4 sm:px-5 py-3.5 flex items-center gap-3 bg-gradient-to-r from-amber-500 to-orange-500">
                                <span class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" /><path d="M14 2v6h6" /><path d="M8 13h8" /><path d="M8 17h8" /></svg>
                                </span>
                                <h3 class="text-sm sm:text-base font-bold text-white tracking-wide">Additional Notes</h3>
                            </div>
                            <div class="p-4 sm:p-5">
                                <p class="text-sm text-gray-700 whitespace-pre-line" x-text="form.notes"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PREVIEW MODE : DOCUMENT PREVIEW ===== -->
                <div x-show="previewMode" x-cloak x-transition class="mt-6">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-slate-50 border-b border-gray-200 px-4 sm:px-5 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                            </div>
                            <span class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold">Document Preview</span>
                        </div>
                        <div class="max-h-[70vh] overflow-y-auto p-5 sm:p-8">
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <!-- Quote header -->
                                <div class="bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-5">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <span class="text-white font-bold">ABRL Test Request Form</span>
                                        <span class="text-white/70 text-xs">Quotation Preview</span>
                                    </div>
                                </div>
                                <div class="p-6 sm:p-8">
                                    <!-- Party -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                                        <div>
                                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Bill To</p>
                                            <p class="text-sm font-semibold text-gray-900" x-text="form.company_name || form.client_name || '-'"></p>
                                            <p class="text-sm text-gray-600" x-text="[form.address, form.address_line2, form.city, form.state, form.postal_code].filter(Boolean).join(', ')"></p>
                                            <p class="text-sm text-gray-600 mt-1" x-show="form.gst_number" x-text="'GST: ' + form.gst_number"></p>
                                            <p class="text-sm text-gray-600 mt-1" x-show="form.different_courier_address" x-text="'Courier: ' + [form.courier_address, form.courier_address_line2, form.courier_city, form.courier_state, form.courier_postal_code].filter(Boolean).join(', ')"></p>
                                        </div>
                                        <div class="sm:text-right">
                                            <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Contact</p>
                                            <p class="text-sm text-gray-600" x-text="form.client_name || '-'"></p>
                                            <p class="text-sm text-gray-600" x-text="form.email"></p>
                                            <p class="text-sm text-gray-600" x-text="form.mobile ? ('+91 ' + form.mobile) : ''"></p>
                                        </div>
                                    </div>

                                    <!-- Sample -->
                                    <div class="rounded-xl bg-slate-50 p-4 mb-6">
                                        <p class="text-[11px] uppercase tracking-wider text-gray-400 font-semibold mb-2">Sample Details</p>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                                            <div>
                                                <p class="text-xs text-gray-400">Sample Name</p>
                                                <p class="font-medium text-gray-900" x-text="form.sample_name || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Batch No.</p>
                                                <p class="font-medium text-gray-900" x-text="form.sample_batch_no || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Physical Form</p>
                                                <p class="font-medium text-gray-900" x-text="form.sample_physical_form || '-'"></p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400">Storage</p>
                                                <p class="font-medium text-gray-900" x-text="form.sample_storage_condition || '-'"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tests table -->
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-left text-gray-400 text-xs uppercase tracking-wider border-b border-gray-200">
                                                <th class="py-2.5 pr-3 font-semibold">Test / Parameter</th>
                                                <th class="py-2.5 px-3 font-semibold">Method</th>
                                                <th class="py-2.5 pl-3 font-semibold text-right">Samples</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(t, idx) in selectedTests" :key="'prev-' + t.id + '-' + idx">
                                                <tr class="border-b border-gray-100 align-top">
                                                    <td class="py-3 pr-3">
                                                        <p class="font-medium text-gray-900" x-text="t.parameter"></p>
                                                        <p class="text-xs text-gray-400 mt-0.5" x-text="[t.discipline, t.material].filter(Boolean).join(' — ')"></p>
                                                    </td>
                                                    <td class="py-3 px-3 text-xs text-gray-600" x-text="t.method || '—'"></td>
                                                    <td class="py-3 pl-3 text-right font-semibold text-gray-900" x-text="t.no_of_samples"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== TERMS & CONDITIONS ===== -->
                <div class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" id="accept_terms" x-model="acceptTerms"
                            class="mt-0.5 w-5 h-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            :class="shouldShowTermsError ? 'border-rose-400 ring-2 ring-rose-200' : ''">
                        <label for="accept_terms" class="block text-sm text-gray-700 cursor-pointer select-none">
                            I have reviewed the preview above and <span class="font-semibold text-gray-900">accept the Terms &amp; Conditions</span>. I understand that by continuing, an official quotation will be generated and a copy will be emailed to <span class="font-semibold text-gray-900" x-text="form.email || 'my email address'"></span>.
                        </label>
                    </div>
                    <p x-show="shouldShowTermsError" x-cloak class="mt-2 text-xs text-rose-500">Please accept the Terms &amp; Conditions to continue.</p>

                    <div class="mt-4 border-t border-gray-100 pt-4 text-xs leading-relaxed text-gray-500">
                        <p class="font-semibold text-gray-700 mb-2">TERMS &amp; CONDITIONS</p>
                        <ol class="list-decimal pl-5 space-y-1.5 text-gray-600">
                            <li>Payment must be made 100% in advance via NEFT, RTGS, or any online payment mode. Cash or cheque payments are not accepted.</li>
                            <li>The analysis will commence only after receipt of full payment. The lead time will begin only after the full advance payment has been received.</li>
                            <li>The estimated timeframe for Test Report delivery is 15 working days under normal conditions. In case of any unforeseen circumstances, the delay will be communicated accordingly.</li>
                            <li>Except for the Test Report, no raw data or additional supporting documents related to the analysis of samples will be provided.</li>
                            <li>Test Reports will be issued in the name specified on the Party Registration &amp; Test Request form, with the same sample name and batch number as mentioned on the received samples.</li>
                            <li>Once the Test Report is generated, no corrections or removal of any parameters will be entertained. Any retesting or reconfirmation of parameters will incur additional charges, subject to the availability of the sample.</li>
                            <li>The customer is required to declare if there is any probable hazard associated with the sample material to ensure the necessary precautions are taken for occupational safety.</li>
                            <li>We do not analyze highly hazardous, flammable, or toxic samples.</li>
                            <li>The customer must declare that they have read and understood the above terms and conditions and confirm that the provided information is true and accurate to the best of their knowledge.</li>
                            <li>By submitting samples for testing, the customer agrees to comply with the above Terms &amp; Conditions.</li>
                        </ol>

                        <p class="font-semibold text-gray-700 mt-4 mb-2">NOTES</p>
                        <ol class="list-decimal pl-5 space-y-1.5 text-gray-600">
                            <li>Perishable samples will be disposed immediately after report dispatch.</li>
                            <li>Non Perishable samples will be stored for one month after Report dispatch or as per the regulatory norms.</li>
                            <li>The information provided by you will be kept confidential and not shared with anybody.</li>
                            <li>The sample to be delivered at: Agri Biochem Research Lab, Plot No. 906/13, Near Ganesh Anand Chokdi, G.I.D.C., Panoli - 394 116, Tal. Ankleshwar, District Bharuch, Gujarat, India. Mob No - +91-9925549313.</li>
                        </ol>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between">
                    <button type="button" @click="step = 3; saveState(); window.scrollTo({top:0, behavior:'smooth'})"
                        class="inline-flex items-center gap-2 text-gray-500 hover:text-gray-700 font-medium text-sm px-4 py-2.5 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5" /><path d="M12 19l-7-7 7-7" /></svg>
                        Back
                    </button>
                    <button type="button" @click="submitQuotation()"
                        :disabled="submitting || !acceptTerms"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-semibold text-sm px-7 py-3.5 rounded-xl shadow-lg shadow-indigo-500/30 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span x-show="submitting" class="spinner"></span>
                        <span x-text="submitting ? 'Generating & sending...' : 'Send to my Email'"></span>
                        <svg x-show="!submitting" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M22 7l-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7" /></svg>
                    </button>
                </div>
            </section>
        </div>
    </main>

    <!-- ======= FOOTER ======= -->
    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-gray-400">&copy; 2026 ABRL. All rights reserved.</p>
        </div>
    </footer>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function quotationApp() {
    return {
        steps: ['User Details', 'Sample Details', 'Select Tests', 'Review'],
        step: 1,
        submittedSteps: [],
        stepErrors: {},
        shownValidation: {},
        firstErrorId: null,
        submitting: false,
        serverError: '',
        previewMode: false,
        acceptTerms: false,
        shouldShowTermsError: false,

        form: {
            client_name: '', company_name: '', email: '', phone: '', mobile_country: '+91', mobile: '',
            address: '', address_line2: '', city: '', state: '', postal_code: '', gst_number: '', pan_number: '',
            different_courier_address: false, courier_address: '', courier_address_line2: '', courier_city: '', courier_state: '', courier_postal_code: '',
            sample_name: '', sample_batch_no: '', sample_physical_form: '', sample_storage_condition: '',
            notes: ''
        },

        cityStateMap: {
            'Ahmedabad': 'Gujarat', 'Surat': 'Gujarat', 'Vadodara': 'Gujarat', 'Rajkot': 'Gujarat',
            'Bhavnagar': 'Gujarat', 'Jamnagar': 'Gujarat', 'Gandhinagar': 'Gujarat', 'Junagadh': 'Gujarat',
            'Gandhidham': 'Gujarat', 'Anand': 'Gujarat', 'Navsari': 'Gujarat', 'Morbi': 'Gujarat',
            'Mehsana': 'Gujarat', 'Nadiad': 'Gujarat', 'Surendranagar': 'Gujarat', 'Bharuch': 'Gujarat',
            'Vapi': 'Gujarat', 'Porbandar': 'Gujarat', 'Amreli': 'Gujarat', 'Valsad': 'Gujarat',
            'Palanpur': 'Gujarat', 'Godhra': 'Gujarat', 'Patan': 'Gujarat', 'Veraval': 'Gujarat',
            'Jetpur': 'Gujarat', 'Botad': 'Gujarat', 'Dahod': 'Gujarat', 'Bhuj': 'Gujarat',
            'Ankleshwar': 'Gujarat', 'Kalol': 'Gujarat', 'Himatnagar': 'Gujarat', 'Dhoraji': 'Gujarat',
            'Dhrangadhra': 'Gujarat', 'Visnagar': 'Gujarat', 'Unjha': 'Gujarat', 'Sidhpur': 'Gujarat',
            'Deesa': 'Gujarat', 'Kadi': 'Gujarat', 'Keshod': 'Gujarat', 'Modasa': 'Gujarat',
            'Pardi': 'Gujarat', 'Sanand': 'Gujarat', 'Vijapur': 'Gujarat', 'Limbdi': 'Gujarat',
            'Wadhwan': 'Gujarat', 'Rajpipla': 'Gujarat', 'Dabhoi': 'Gujarat', 'Bardoli': 'Gujarat',
            'Tankara': 'Gujarat', 'Idar': 'Gujarat', 'Mahuva': 'Gujarat', 'Wankaner': 'Gujarat',
            'Mundra': 'Gujarat', 'Umbergaon': 'Gujarat', 'Chotila': 'Gujarat', 'Bayad': 'Gujarat',
            'Garbada': 'Gujarat', 'Kapadvanj': 'Gujarat', 'Thangadh': 'Gujarat', 'Vijaynagar': 'Gujarat',
            'Amod': 'Gujarat', 'Jambusar': 'Gujarat', 'Palitana': 'Gujarat', 'Rajula': 'Gujarat',
            'Kutiyana': 'Gujarat', 'Manavadar': 'Gujarat', 'Bansda': 'Gujarat',
            'Chanasma': 'Gujarat', 'Vadnagar': 'Gujarat', 'Kheralu': 'Gujarat', 'Vijaypur': 'Gujarat',
            'Mumbai': 'Maharashtra', 'Pune': 'Maharashtra', 'Nagpur': 'Maharashtra', 'Nashik': 'Maharashtra',
            'Thane': 'Maharashtra', 'Aurangabad': 'Maharashtra', 'Solapur': 'Maharashtra', 'Kolhapur': 'Maharashtra',
            'Amravati': 'Maharashtra', 'Nanded': 'Maharashtra', 'Sangli': 'Maharashtra', 'Jalgaon': 'Maharashtra',
            'Akola': 'Maharashtra', 'Latur': 'Maharashtra', 'Dhule': 'Maharashtra', 'Ahmednagar': 'Maharashtra',
            'Chandrapur': 'Maharashtra', 'Parbhani': 'Maharashtra', 'Ichalkaranji': 'Maharashtra', 'Jalna': 'Maharashtra',
            'Bhusawal': 'Maharashtra', 'Satara': 'Maharashtra', 'Beed': 'Maharashtra', 'Pimpri-Chinchwad': 'Maharashtra',
            'Panvel': 'Maharashtra', 'Bhiwandi': 'Maharashtra', 'Vasai': 'Maharashtra', 'Virar': 'Maharashtra',
            'Lonavala': 'Maharashtra', 'Ratnagiri': 'Maharashtra', 'Wardha': 'Maharashtra', 'Yavatmal': 'Maharashtra',
            'Washim': 'Maharashtra', 'Osmanabad': 'Maharashtra', 'Hingoli': 'Maharashtra', 'Buldhana': 'Maharashtra',
            'Gondia': 'Maharashtra', 'Gadchiroli': 'Maharashtra', 'Bhandara': 'Maharashtra', 'Miraj': 'Maharashtra',
            'Delhi': 'Delhi', 'New Delhi': 'Delhi',
            'Jaipur': 'Rajasthan', 'Jodhpur': 'Rajasthan', 'Udaipur': 'Rajasthan', 'Kota': 'Rajasthan',
            'Bikaner': 'Rajasthan', 'Ajmer': 'Rajasthan', 'Bhilwara': 'Rajasthan', 'Alwar': 'Rajasthan',
            'Sikar': 'Rajasthan', 'Bharatpur': 'Rajasthan', 'Pali': 'Rajasthan', 'Sri Ganganagar': 'Rajasthan',
            'Hanumangarh': 'Rajasthan', 'Tonk': 'Rajasthan', 'Kishangarh': 'Rajasthan', 'Beawar': 'Rajasthan',
            'Churu': 'Rajasthan', 'Jhunjhunu': 'Rajasthan', 'Nagaur': 'Rajasthan', 'Barmer': 'Rajasthan',
            'Jaisalmer': 'Rajasthan', 'Sawai Madhopur': 'Rajasthan', 'Dausa': 'Rajasthan', 'Chittorgarh': 'Rajasthan',
            'Dholpur': 'Rajasthan', 'Sirohi': 'Rajasthan', 'Banswara': 'Rajasthan', 'Rajsamand': 'Rajasthan',
            'Karauli': 'Rajasthan', 'Pratapgarh': 'Rajasthan', 'Gangapur City': 'Rajasthan', 'Makrana': 'Rajasthan',
            'Bengaluru': 'Karnataka', 'Mysuru': 'Karnataka', 'Mangaluru': 'Karnataka', 'Hubballi': 'Karnataka',
            'Belagavi': 'Karnataka', 'Kalaburagi': 'Karnataka', 'Davangere': 'Karnataka', 'Ballari': 'Karnataka',
            'Vijayapura': 'Karnataka', 'Shivamogga': 'Karnataka', 'Tumakuru': 'Karnataka', 'Raichur': 'Karnataka',
            'Bidar': 'Karnataka', 'Hospet': 'Karnataka', 'Gadag': 'Karnataka', 'Hassan': 'Karnataka',
            'Mandya': 'Karnataka', 'Udupi': 'Karnataka', 'Chikkamagaluru': 'Karnataka', 'Kolar': 'Karnataka',
            'Shimoga': 'Karnataka', 'Bagalkot': 'Karnataka', 'Dharwad': 'Karnataka', 'Karwar': 'Karnataka',
            'Bhatkal': 'Karnataka', 'Chitradurga': 'Karnataka', 'Madikeri': 'Karnataka', 'Ramanagara': 'Karnataka',
            'Hyderabad': 'Telangana', 'Warangal': 'Telangana', 'Nizamabad': 'Telangana', 'Karimnagar': 'Telangana',
            'Khammam': 'Telangana', 'Ramagundam': 'Telangana', 'Mahbubnagar': 'Telangana', 'Nalgonda': 'Telangana',
            'Adilabad': 'Telangana', 'Suryapet': 'Telangana', 'Miryalaguda': 'Telangana', 'Siddipet': 'Telangana',
            'Chennai': 'Tamil Nadu', 'Coimbatore': 'Tamil Nadu', 'Madurai': 'Tamil Nadu', 'Salem': 'Tamil Nadu',
            'Tiruchirappalli': 'Tamil Nadu', 'Tiruppur': 'Tamil Nadu', 'Vellore': 'Tamil Nadu', 'Erode': 'Tamil Nadu',
            'Thoothukudi': 'Tamil Nadu', 'Dindigul': 'Tamil Nadu', 'Thanjavur': 'Tamil Nadu', 'Kumbakonam': 'Tamil Nadu',
            'Kancheepuram': 'Tamil Nadu', 'Nagercoil': 'Tamil Nadu', 'Cuddalore': 'Tamil Nadu', 'Tirunelveli': 'Tamil Nadu',
            'Karur': 'Tamil Nadu', 'Namakkal': 'Tamil Nadu', 'Ramanathapuram': 'Tamil Nadu', 'Villupuram': 'Tamil Nadu',
            'Tenkasi': 'Tamil Nadu', 'Pudukkottai': 'Tamil Nadu', 'Nagapattinam': 'Tamil Nadu', 'Hosur': 'Tamil Nadu',
            'Kolkata': 'West Bengal', 'Howrah': 'West Bengal', 'Siliguri': 'West Bengal', 'Durgapur': 'West Bengal',
            'Asansol': 'West Bengal', 'Malda': 'West Bengal', 'Kharagpur': 'West Bengal', 'Haldia': 'West Bengal',
            'Bardhaman': 'West Bengal', 'Krishnanagar': 'West Bengal', 'Baharampur': 'West Bengal', 'Jalpaiguri': 'West Bengal',
            'Darjeeling': 'West Bengal', 'Medinipur': 'West Bengal', 'Raiganj': 'West Bengal', 'Cooch Behar': 'West Bengal',
            'Alipore': 'West Bengal', 'Bally': 'West Bengal', 'Barasat': 'West Bengal', 'English Bazar': 'West Bengal',
            'Lucknow': 'Uttar Pradesh', 'Kanpur': 'Uttar Pradesh', 'Varanasi': 'Uttar Pradesh', 'Agra': 'Uttar Pradesh',
            'Meerut': 'Uttar Pradesh', 'Prayagraj': 'Uttar Pradesh', 'Bareilly': 'Uttar Pradesh', 'Gorakhpur': 'Uttar Pradesh',
            'Aligarh': 'Uttar Pradesh', 'Moradabad': 'Uttar Pradesh', 'Saharanpur': 'Uttar Pradesh', 'Jhansi': 'Uttar Pradesh',
            'Noida': 'Uttar Pradesh', 'Ghaziabad': 'Uttar Pradesh', 'Mathura': 'Uttar Pradesh', 'Firozabad': 'Uttar Pradesh',
            'Muzaffarnagar': 'Uttar Pradesh', 'Rampur': 'Uttar Pradesh', 'Faizabad': 'Uttar Pradesh', 'Ghazipur': 'Uttar Pradesh',
            'Etawah': 'Uttar Pradesh', 'Budaun': 'Uttar Pradesh', 'Raebareli': 'Uttar Pradesh', 'Sitapur': 'Uttar Pradesh',
            'Banda': 'Uttar Pradesh', 'Bahraich': 'Uttar Pradesh', 'Ballia': 'Uttar Pradesh', 'Etah': 'Uttar Pradesh',
            'Hapur': 'Uttar Pradesh', 'Unnao': 'Uttar Pradesh', 'Lalitpur': 'Uttar Pradesh', 'Bulandshahr': 'Uttar Pradesh',
            'Bhopal': 'Madhya Pradesh', 'Indore': 'Madhya Pradesh', 'Gwalior': 'Madhya Pradesh', 'Jabalpur': 'Madhya Pradesh',
            'Ujjain': 'Madhya Pradesh', 'Sagar': 'Madhya Pradesh', 'Dewas': 'Madhya Pradesh', 'Satna': 'Madhya Pradesh',
            'Ratlam': 'Madhya Pradesh', 'Rewa': 'Madhya Pradesh', 'Murwara': 'Madhya Pradesh', 'Singrauli': 'Madhya Pradesh',
            'Burhanpur': 'Madhya Pradesh', 'Khandwa': 'Madhya Pradesh', 'Khargone': 'Madhya Pradesh', 'Chhindwara': 'Madhya Pradesh',
            'Betul': 'Madhya Pradesh', 'Guna': 'Madhya Pradesh', 'Sehore': 'Madhya Pradesh', 'Morena': 'Madhya Pradesh',
            'Vidisha': 'Madhya Pradesh', 'Damoh': 'Madhya Pradesh', 'Neemuch': 'Madhya Pradesh', 'Mandsaur': 'Madhya Pradesh',
            'Kochi': 'Kerala', 'Thiruvananthapuram': 'Kerala', 'Kozhikode': 'Kerala', 'Kollam': 'Kerala',
            'Thrissur': 'Kerala', 'Alappuzha': 'Kerala', 'Kannur': 'Kerala', 'Kottayam': 'Kerala',
            'Palakkad': 'Kerala', 'Malappuram': 'Kerala', 'Kasaragod': 'Kerala', 'Pathanamthitta': 'Kerala',
            'Idukki': 'Kerala', 'Wayanad': 'Kerala', 'Ernakulam': 'Kerala',
            'Chandigarh': 'Chandigarh', 'Amritsar': 'Punjab', 'Ludhiana': 'Punjab', 'Jalandhar': 'Punjab',
            'Patiala': 'Punjab', 'Bathinda': 'Punjab', 'Mohali': 'Punjab', 'Firozpur': 'Punjab',
            'Moga': 'Punjab', 'Pathankot': 'Punjab', 'Khanna': 'Punjab', 'Phagwara': 'Punjab',
            'Hoshiarpur': 'Punjab', 'Sangrur': 'Punjab', 'Barnala': 'Punjab', 'Fazilka': 'Punjab',
            'Gurdaspur': 'Punjab', 'Faridkot': 'Punjab', 'Kapurthala': 'Punjab', 'Nawanshahr': 'Punjab',
            'Patna': 'Bihar', 'Gaya': 'Bihar', 'Darbhanga': 'Bihar', 'Muzaffarpur': 'Bihar',
            'Bhagalpur': 'Bihar', 'Purnia': 'Bihar', 'Bihar Sharif': 'Bihar', 'Arrah': 'Bihar',
            'Begusarai': 'Bihar', 'Katihar': 'Bihar', 'Munger': 'Bihar', 'Chhapra': 'Bihar',
            'Saharsa': 'Bihar', 'Sasaram': 'Bihar', 'Hajipur': 'Bihar', 'Dehri': 'Bihar',
            'Siwan': 'Bihar', 'Motihari': 'Bihar', 'Nawada': 'Bihar', 'Bettiah': 'Bihar',
            'Ranchi': 'Jharkhand', 'Jamshedpur': 'Jharkhand', 'Dhanbad': 'Jharkhand', 'Bokaro': 'Jharkhand',
            'Hazaribagh': 'Jharkhand', 'Deoghar': 'Jharkhand', 'Giridih': 'Jharkhand', 'Jamtara': 'Jharkhand',
            'Ramgarh': 'Jharkhand', 'Phusro': 'Jharkhand', 'Gumla': 'Jharkhand', 'Chaibasa': 'Jharkhand',
            'Bhubaneswar': 'Odisha', 'Cuttack': 'Odisha', 'Rourkela': 'Odisha', 'Berhampur': 'Odisha',
            'Sambalpur': 'Odisha', 'Puri': 'Odisha', 'Balasore': 'Odisha', 'Bhadrak': 'Odisha',
            'Baripada': 'Odisha', 'Jharsuguda': 'Odisha', 'Angul': 'Odisha', 'Paradeep': 'Odisha',
            'Guwahati': 'Assam', 'Silchar': 'Assam', 'Dibrugarh': 'Assam', 'Jorhat': 'Assam',
            'Tezpur': 'Assam', 'Nagaon': 'Assam', 'Tinsukia': 'Assam', 'Bongaigaon': 'Assam',
            'Diphu': 'Assam', 'North Lakhimpur': 'Assam', 'Karimganj': 'Assam', 'Sivasagar': 'Assam',
            'Raipur': 'Chhattisgarh', 'Bilaspur': 'Chhattisgarh', 'Korba': 'Chhattisgarh', 'Bhilai': 'Chhattisgarh',
            'Durg': 'Chhattisgarh', 'Raigarh': 'Chhattisgarh', 'Rajnandgaon': 'Chhattisgarh', 'Jagdalpur': 'Chhattisgarh',
            'Ambikapur': 'Chhattisgarh', 'Mahasamund': 'Chhattisgarh', 'Dhamtari': 'Chhattisgarh', 'Bhawanipatna': 'Odisha',
            'Panaji': 'Goa', 'Vasco da Gama': 'Goa', 'Margao': 'Goa', 'Mapusa': 'Goa',
            'Ponda': 'Goa', 'Bicholim': 'Goa', 'Curchorem': 'Goa', 'Cuncolim': 'Goa',
            'Dehradun': 'Uttarakhand', 'Haridwar': 'Uttarakhand', 'Rishikesh': 'Uttarakhand', 'Nainital': 'Uttarakhand',
            'Haldwani': 'Uttarakhand', 'Roorkee': 'Uttarakhand', 'Kashipur': 'Uttarakhand', 'Rudrapur': 'Uttarakhand',
            'Almora': 'Uttarakhand', 'Pithoragarh': 'Uttarakhand', 'Pauri': 'Uttarakhand', 'Mussoorie': 'Uttarakhand',
            'Shimla': 'Himachal Pradesh', 'Manali': 'Himachal Pradesh', 'Dharamshala': 'Himachal Pradesh', 'Solan': 'Himachal Pradesh',
            'Mandi': 'Himachal Pradesh', 'Bilaspur (HP)': 'Himachal Pradesh', 'Chamba': 'Himachal Pradesh', 'Kullu': 'Himachal Pradesh',
            'Srinagar': 'Jammu and Kashmir', 'Jammu': 'Jammu and Kashmir', 'Anantnag': 'Jammu and Kashmir', 'Baramulla': 'Jammu and Kashmir',
            'Sopore': 'Jammu and Kashmir', 'Katra': 'Jammu and Kashmir', 'Udhampur': 'Jammu and Kashmir', 'Pulwama': 'Jammu and Kashmir',
            'Kupwara': 'Jammu and Kashmir', 'Rajouri': 'Jammu and Kashmir', 'Doda': 'Jammu and Kashmir', 'Poonch': 'Jammu and Kashmir',
            'Leh': 'Ladakh', 'Kargil': 'Ladakh',
            'Agartala': 'Tripura', 'Udaipur (Tripura)': 'Tripura', 'Dharmanagar': 'Tripura', 'Kailashahar': 'Tripura',
            'Shillong': 'Meghalaya', 'Tura': 'Meghalaya', 'Jowai': 'Meghalaya', 'Nongstoin': 'Meghalaya',
            'Imphal': 'Manipur', 'Thoubal': 'Manipur', 'Bishnupur': 'Manipur', 'Kakching': 'Manipur',
            'Aizawl': 'Mizoram', 'Lunglei': 'Mizoram', 'Champhai': 'Mizoram', 'Serchhip': 'Mizoram',
            'Kohima': 'Nagaland', 'Dimapur': 'Nagaland', 'Mokokchung': 'Nagaland', 'Wokha': 'Nagaland',
            'Gangtok': 'Sikkim', 'Namchi': 'Sikkim', 'Gyalshing': 'Sikkim', 'Mangan': 'Sikkim',
            'Itanagar': 'Arunachal Pradesh', 'Naharlagun': 'Arunachal Pradesh', 'Pasighat': 'Arunachal Pradesh', 'Bomdila': 'Arunachal Pradesh',
            'Puducherry': 'Puducherry', 'Karaikal': 'Puducherry', 'Yanam': 'Puducherry', 'Mahe': 'Puducherry',
            'Port Blair': 'Andaman and Nicobar Islands', 'Kavaratti': 'Lakshadweep', 'Silvassa': 'Dadra and Nagar Haveli and Daman and Diu', 'Daman': 'Dadra and Nagar Haveli and Daman and Diu', 'Diu': 'Dadra and Nagar Haveli and Daman and Diu'
        },

        cityOptions() {
            return Object.keys(this.cityStateMap).sort();
        },

        onCityChange() {
            if (this.form.city && this.cityStateMap.hasOwnProperty(this.form.city)) {
                this.form.state = this.cityStateMap[this.form.city];
            } else {
                this.form.state = '';
            }
        },

        onCourierCityChange() {
            if (this.form.courier_city && this.cityStateMap.hasOwnProperty(this.form.courier_city)) {
                this.form.courier_state = this.cityStateMap[this.form.courier_city];
            } else {
                this.form.courier_state = '';
            }
        },

        optionsUrl: '{{ route("lab-tests.options") }}',

        nablOptions: [],
        disciplineOptions: [],
        materialOptions: [],
        parameterOptions: [],

        currentNabl: '',
        currentDiscipline: '',
        currentMaterial: '',
        currentParameter: null,
        noOfSamples: 1,
        loadingCascade: false,
        cascadeSeq: 0,

        selectedTests: [],

        maxTests: 5,
        currentSlotIndex: 0,
        slots: Array.from({ length: 5 }, (_, i) => ({
            nabl: '', discipline: '', material: '', parameter: null,
            disciplineOptions: [], materialOptions: [], parameterOptions: [],
            loading: false, _seq: 0, index: i + 1
        })),

        activeSlot() {
            if (this.currentSlotIndex >= this.maxTests) return null;
            return this.slots[this.currentSlotIndex];
        },

        _saveKey: 'abrl_quotation_form',

        saveState() {
            const state = {
                step: this.step,
                form: { ...this.form },
                selectedTests: this.selectedTests.map(t => ({
                    id: t.id, parameter: t.parameter, discipline: t.discipline, material: t.material,
                    method: t.method, sample_quantity: t.sample_quantity, lead_time: t.lead_time,
                    protocol_no: t.protocol_no, nabl_range: t.nabl_range,
                    limit_of_quantification: t.limit_of_quantification, remarks: t.remarks,
                    protocol_link: t.protocol_link, no_of_samples: t.no_of_samples
                })),
                currentSlotIndex: this.currentSlotIndex,
                acceptTerms: this.acceptTerms,
                submittedSteps: [...this.submittedSteps],
            };
            try { localStorage.setItem(this._saveKey, JSON.stringify(state)); } catch (e) {}
        },

        restoreState() {
            try {
                const raw = localStorage.getItem(this._saveKey);
                if (!raw) return false;
                const state = JSON.parse(raw);
                if (state.step) this.step = state.step;
                if (state.form) Object.assign(this.form, state.form);
                if (state.selectedTests) this.selectedTests = state.selectedTests;
                if (state.currentSlotIndex !== undefined) this.currentSlotIndex = state.currentSlotIndex;
                if (state.acceptTerms) this.acceptTerms = state.acceptTerms;
                if (state.submittedSteps) this.submittedSteps = state.submittedSteps;
                return true;
            } catch (e) { return false; }
        },

        clearState() {
            try { localStorage.removeItem(this._saveKey); } catch (e) {}
        },

        hasSavedState() {
            try { return !!localStorage.getItem(this._saveKey); } catch (e) { return false; }
        },

        resetForm() {
            if (!confirm('Start over and clear all entered details?')) return;
            this.clearState();
            window.location.reload();
        },

        initApp() {
            this.restoreState();
            this.loadOptions({});
        },

        async loadOptions(params, level) {
            const seq = ++this.cascadeSeq;
            this.loadingCascade = true;
            this.serverError = '';
            try {
                const qs = new URLSearchParams(params).toString();
                const res = await fetch(this.optionsUrl + (qs ? '?' + qs : ''), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to load options');
                if (seq !== this.cascadeSeq) return;
                if (data.level === 'nabl') {
                    this.nablOptions = data.options || [];
                } else if (data.level === 'discipline') {
                    this.disciplineOptions = data.options || [];
                } else if (data.level === 'material') {
                    this.materialOptions = data.options || [];
                } else if (data.level === 'parameter') {
                    this.parameterOptions = data.options || [];
                }
            } catch (e) {
                if (seq === this.cascadeSeq) this.serverError = 'Unable to load options right now. Please refresh the page.';
            } finally {
                if (seq === this.cascadeSeq) this.loadingCascade = false;
            }
        },

        selectNabl(type) {
            if (this.currentNabl === type) { this.resetAfter('nabl'); return; }
            this.currentNabl = type;
            this.resetChildren('discipline');
            this.loadOptions({ nabl: type });
        },

        selectDiscipline(d) {
            if (this.currentDiscipline === d) { this.resetAfter('discipline'); return; }
            this.currentDiscipline = d;
            this.resetChildren('material');
            this.loadOptions({ nabl: this.currentNabl, discipline: d });
        },

        selectMaterial(m) {
            if (this.currentMaterial === m) { this.resetAfter('material'); return; }
            this.currentMaterial = m;
            this.resetChildren('parameter');
            this.loadOptions({ nabl: this.currentNabl, discipline: this.currentDiscipline, material: m });
        },

        resetAfter(level) {
            if (level === 'nabl') { this.resetChildren('discipline'); }
            else if (level === 'discipline') { this.resetChildren('material'); }
            else if (level === 'material') { this.resetChildren('parameter'); }
        },

        resetChildren(level) {
            if (level === 'discipline') {
                this.currentDiscipline = '';
                this.disciplineOptions = [];
            }
            if (level === 'discipline' || level === 'material') {
                this.currentMaterial = '';
                this.materialOptions = [];
            }
            if (level === 'discipline' || level === 'material' || level === 'parameter') {
                this.currentParameter = null;
                this.parameterOptions = [];
                this.noOfSamples = 1;
            }
        },

        addTest() {
            if (!this.currentParameter) return;
            if (this.selectedTests.length >= this.maxTests) {
                this.stepErrors[3] = [`Maximum ${this.maxTests} tests allowed per quotation. Please submit this form and create a new one for additional tests.`];
                return;
            }
            const samples = Math.max(1, Number(this.noOfSamples) || 1);
            this.selectedTests.push({
                id: this.currentParameter.id,
                parameter: this.currentParameter.parameter,
                discipline: this.currentParameter.discipline,
                material: this.currentParameter.material,
                method: this.currentParameter.method,
                sample_quantity: this.currentParameter.sample_quantity,
                lead_time: this.currentParameter.lead_time,
                protocol_no: this.currentParameter.protocol_no,
                nabl_range: this.currentParameter.nabl_range,
                limit_of_quantification: this.currentParameter.limit_of_quantification,
                remarks: this.currentParameter.remarks,
                protocol_link: this.currentParameter.protocol_link,
                no_of_samples: samples
            });
            this.currentParameter = null;
            this.noOfSamples = 1;
            if (this.currentSlotIndex < this.maxTests - 1) {
                this.currentSlotIndex++;
            }
            this.stepErrors[2] = [];
            this.stepErrors[3] = [];
            this.saveState();
        },

        removeTest(idx) {
            this.selectedTests.splice(idx, 1);
            const next = this.selectedTests.length;
            if (next < this.currentSlotIndex) {
                const s = this.slots[next];
                s.nabl = ''; s.discipline = ''; s.material = ''; s.parameter = null;
                s.disciplineOptions = []; s.materialOptions = []; s.parameterOptions = [];
            }
            this.currentSlotIndex = Math.min(next, this.maxTests - 1);
            this.saveState();
        },

        async slotLoad(kind, i) {
            const s = this.slots[i];
            const seq = ++s._seq;
            s.loading = true;
            const params = {};
            if (s.nabl) params.nabl = s.nabl;
            if (s.discipline) params.discipline = s.discipline;
            if (s.material) params.material = s.material;
            const qs = new URLSearchParams(params).toString();
            try {
                const res = await fetch(this.optionsUrl + (qs ? '?' + qs : ''), {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to load options');
                if (seq !== s._seq) return;
                if (data.level === 'discipline') s.disciplineOptions = data.options || [];
                else if (data.level === 'material') s.materialOptions = data.options || [];
                else if (data.level === 'parameter') s.parameterOptions = data.options || [];
            } catch (e) {
                if (seq === s._seq) this.serverError = 'Unable to load options right now. Please refresh the page.';
            } finally {
                if (seq === s._seq) s.loading = false;
            }
        },

        onSlotNabl(i) {
            const s = this.slots[i];
            s.discipline = ''; s.material = ''; s.parameter = null;
            s.disciplineOptions = []; s.materialOptions = []; s.parameterOptions = [];
            if (s.nabl) this.slotLoad('discipline', i);
        },

        onSlotDiscipline(i) {
            const s = this.slots[i];
            s.material = ''; s.parameter = null;
            s.materialOptions = []; s.parameterOptions = [];
            if (s.discipline) this.slotLoad('material', i);
        },

        onSlotMaterial(i) {
            const s = this.slots[i];
            s.parameter = null;
            s.parameterOptions = [];
            if (s.material) this.slotLoad('parameter', i);
        },

        onSlotParameter(i, event) {
            const s = this.slots[i];
            const id = parseInt(event.target.value, 10);
            s.parameter = (s.parameterOptions || []).find(p => p.id === id) || null;
        },

        addFromSlot(i) {
            const s = this.slots[i];
            if (!s.parameter) return;
            if (this.selectedTests.length >= this.maxTests) {
                this.stepErrors[3] = [`Maximum ${this.maxTests} tests allowed per quotation. Please submit this form and create a new one for additional tests.`];
                return;
            }
            const t = s.parameter;
            this.selectedTests.push({
                id: t.id, parameter: t.parameter, discipline: t.discipline, material: t.material,
                method: t.method, sample_quantity: t.sample_quantity, lead_time: t.lead_time,
                protocol_no: t.protocol_no, nabl_range: t.nabl_range,
                limit_of_quantification: t.limit_of_quantification, remarks: t.remarks,
                protocol_link: t.protocol_link, no_of_samples: 1
            });
            if (this.currentSlotIndex < this.maxTests - 1) {
                this.currentSlotIndex++;
            }
            this.stepErrors[2] = [];
            this.stepErrors[3] = [];
            this.saveState();
        },

        shouldShowValidation(step) {
            return (this.shownValidation[step] || false) && (this.stepErrors[step] || []).length > 0;
        },

        validateStep(n) {
            this.stepErrors[n] = [];
            this.shownValidation[n] = true;
            this.firstErrorId = null;

            if (n === 1) {
                if (!this.form.company_name.trim()) { this.stepErrors[1].push('Please enter company name.'); if (!this.firstErrorId) this.firstErrorId = 'company_name'; }
                if (!this.form.address.trim()) { this.stepErrors[1].push('Please enter complete address.'); if (!this.firstErrorId) this.firstErrorId = 'address'; }
                if (!this.form.city) { this.stepErrors[1].push('Please select city.'); if (!this.firstErrorId) this.firstErrorId = 'city'; }
                if (!this.form.postal_code.trim()) { this.stepErrors[1].push('Please enter postal code.'); if (!this.firstErrorId) this.firstErrorId = 'postal_code'; }
                if (this.form.different_courier_address) {
                    if (!this.form.courier_address.trim()) { this.stepErrors[1].push('Please enter courier address.'); if (!this.firstErrorId) this.firstErrorId = 'courier_address'; }
                    if (!this.form.courier_city) { this.stepErrors[1].push('Please select courier city.'); if (!this.firstErrorId) this.firstErrorId = 'courier_city'; }
                    if (!this.form.courier_postal_code.trim()) { this.stepErrors[1].push('Please enter courier postal code.'); if (!this.firstErrorId) this.firstErrorId = 'courier_postal_code'; }
                }
                if (!this.form.client_name.trim()) { this.stepErrors[1].push('Please enter contact person name.'); if (!this.firstErrorId) this.firstErrorId = 'client_name'; }
                if (!this.form.mobile.trim()) { this.stepErrors[1].push('Please enter mobile number.'); if (!this.firstErrorId) this.firstErrorId = 'mobile'; }
                else if (!/^[6-9]\d{9}$/.test(this.form.mobile.trim())) { this.stepErrors[1].push('Please enter a valid 10-digit Indian mobile number.'); if (!this.firstErrorId) this.firstErrorId = 'mobile'; }
                if (!this.form.email.trim()) { this.stepErrors[1].push('Please enter your email address.'); if (!this.firstErrorId) this.firstErrorId = 'email'; }
                else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) { this.stepErrors[1].push('Please enter a valid email address.'); if (!this.firstErrorId) this.firstErrorId = 'email'; }
                if (!this.form.gst_number.trim()) { this.stepErrors[1].push('Please enter GST number.'); if (!this.firstErrorId) this.firstErrorId = 'gst_number'; }
                else if (!/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][0-9A-Z]Z[0-9A-Z]$/.test(this.form.gst_number.trim())) { this.stepErrors[1].push('Please enter a valid 15-character GSTIN.'); if (!this.firstErrorId) this.firstErrorId = 'gst_number'; }
                if (!this.form.pan_number.trim()) { this.stepErrors[1].push('Please enter PAN number.'); if (!this.firstErrorId) this.firstErrorId = 'pan_number'; }
                else if (!/^[A-Z]{5}[0-9]{4}[A-Z]$/.test(this.form.pan_number.trim())) { this.stepErrors[1].push('Please enter a valid 10-character PAN.'); if (!this.firstErrorId) this.firstErrorId = 'pan_number'; }
            }

            if (n === 2) {
                if (!this.form.sample_name.trim()) { this.stepErrors[2].push('Please enter sample name.'); if (!this.firstErrorId) this.firstErrorId = 'sample_name'; }
                if (!this.form.sample_batch_no.trim()) { this.stepErrors[2].push('Please enter sample batch no.'); if (!this.firstErrorId) this.firstErrorId = 'sample_batch_no'; }
                if (!this.form.sample_physical_form) { this.stepErrors[2].push('Please select sample physical form.'); if (!this.firstErrorId) this.firstErrorId = 'sample_physical_form'; }
                if (!this.form.sample_storage_condition) { this.stepErrors[2].push('Please select storage condition.'); if (!this.firstErrorId) this.firstErrorId = 'sample_storage_condition'; }
            }

            if (n === 3) {
                if (this.selectedTests.length === 0) this.stepErrors[3].push('Please select at least one test.');
            }

            if (this.stepErrors[n].length === 0) {
                if (!this.submittedSteps.includes(n)) this.submittedSteps.push(n);
                this.step = n + 1;
                this.saveState();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                this.$nextTick(() => {
                    if (this.firstErrorId) {
                        const el = document.getElementById(this.firstErrorId);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            }
        },

        gotoStep(i) {
            const target = i + 1;
            if (this.step > target) {
                this.step = target;
                this.saveState();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            for (let s = target - 1; s >= 1; s--) {
                if (!this.submittedSteps.includes(s)) return;
            }
            this.step = target;
            this.saveState();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        submitQuotation() {
            if (this.submitting) return;
            if (this.selectedTests.length === 0) { this.serverError = 'Please select at least one test.'; return; }
            if (!this.acceptTerms) {
                this.shouldShowTermsError = true;
                this.$nextTick(() => {
                    const el = document.getElementById('accept_terms');
                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
                return;
            }
            this.shouldShowTermsError = false;
            this.submitting = true;
            this.serverError = '';

            const payload = {
                client_name: this.form.client_name,
                company_name: this.form.company_name,
                email: this.form.email,
                mobile_country: '+91',
                mobile: this.form.mobile,
                address: this.form.address,
                address_line2: this.form.address_line2,
                city: this.form.city,
                state: this.form.state,
                postal_code: this.form.postal_code,
                different_courier_address: this.form.different_courier_address,
                courier_address: this.form.different_courier_address ? this.form.courier_address : '',
                courier_address_line2: this.form.different_courier_address ? this.form.courier_address_line2 : '',
                courier_city: this.form.different_courier_address ? this.form.courier_city : '',
                courier_state: this.form.different_courier_address ? this.form.courier_state : '',
                courier_postal_code: this.form.different_courier_address ? this.form.courier_postal_code : '',
                gst_number: this.form.gst_number,
                pan_number: this.form.pan_number,
                sample_name: this.form.sample_name,
                sample_batch_no: this.form.sample_batch_no,
                sample_physical_form: this.form.sample_physical_form,
                sample_storage_condition: this.form.sample_storage_condition,
                notes: this.form.notes,
                lab_tests: this.selectedTests.map(t => ({
                    lab_test_id: t.id,
                    no_of_samples: Math.max(1, Number(t.no_of_samples) || 1),
                    notes: ''
                }))
            };
            fetch('{{ route("quotation.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            })
            .then(r => r.json().then(data => ({ ok: r.ok, status: r.status, data })))
            .then(({ ok, status, data }) => {
                if (ok && data.redirect) {
                    this.clearState();
                    window.location.href = data.redirect;
                } else {
                    this.serverError = data.message || data.errors
                        ? (data.errors && data.errors.lab_tests ? data.errors.lab_tests[0] : (data.message || 'Something went wrong. Please try again.'))
                        : 'Something went wrong. Please try again.';
                    this.submitting = false;
                }
            })
            .catch(() => {
                this.serverError = 'Network error. Please check your connection and try again.';
                this.submitting = false;
            });
        }
    };
}
</script>
@if ($errors->any())
<script>
    window.addEventListener('load', function () {
        const msgs = @json($errors->all());
        if (msgs.length) {
            alert(msgs.join('\n'));
        }
    });
</script>
@endif
</body>
</html>
