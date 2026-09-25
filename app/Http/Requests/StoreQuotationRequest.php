<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $mobile = trim((string) $this->input('mobile'));
        $country = trim((string) $this->input('mobile_country'), " \t\n\r\0\x0B+");
        $existingPhone = trim((string) $this->input('phone'));

        $phone = $existingPhone ?: '';

        if ($phone === '' && $mobile !== '') {
            $phone = $country !== '' ? $country . ' ' . $mobile : $mobile;
        }

        $this->merge(['phone' => $phone ?: null]);
    }

    public function rules(): array
    {
        $country = trim((string) $this->input('country', 'India'));
        $mobileRules = ['required', 'string', 'max:20'];
        if ($country === 'India') {
            $mobileRules[] = 'regex:/^[6-9]\d{9}$/';
        } else {
            $mobileRules[] = 'regex:/^[0-9][0-9\s\-()]{5,19}$/';
        }
        $gstRules = ['nullable', 'string', 'max:50'];

        return [
            'client_name' => ['required', 'string', 'max:191'],
            'company_name' => ['nullable', 'string', 'max:191'],
            'sample_name' => ['nullable', 'string', 'max:191'],
            'sample_batch_no' => ['nullable', 'string', 'max:191'],
            'sample_physical_form' => ['nullable', 'string', 'max:191'],
            'sample_storage_condition' => ['nullable', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'mobile' => $mobileRules,
            'mobile_country' => ['required', 'string', 'max:6'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:191'],
            'state' => ['nullable', 'string', 'max:191'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'different_courier_address' => ['sometimes', 'boolean'],
            'courier_address' => ['nullable', 'string', 'max:500'],
            'courier_city' => ['nullable', 'string', 'max:191'],
            'courier_state' => ['nullable', 'string', 'max:191'],
            'courier_postal_code' => ['nullable', 'string', 'max:20'],
            'courier_country' => ['nullable', 'string', 'max:191'],
            'country' => ['nullable', 'string', 'max:191'],
            'gst_number' => $gstRules,
            'services' => ['sometimes', 'array', 'min:1'],
            'services.*.service_id' => ['required_with:services', 'integer'],
            'services.*.quantity' => ['required_with:services', 'integer', 'min:1', 'max:9999'],
            'services.*.discount' => ['nullable', 'numeric', 'min:0'],
            'services.*.notes' => ['nullable', 'string', 'max:1000'],
            'lab_tests' => ['sometimes', 'array', 'min:1'],
            'lab_tests.*.lab_test_id' => ['required_with:lab_tests', 'integer'],
            'lab_tests.*.no_of_samples' => ['required_with:lab_tests', 'integer', 'min:1', 'max:9999'],
            'lab_tests.*.notes' => ['nullable', 'string', 'max:1000'],
            'project_description' => ['nullable', 'string', 'max:5000'],
            'additional_requirements' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'expected_timeline' => ['nullable', 'string', 'max:191'],
            'preferred_contact_method' => ['nullable', 'string', 'max:191'],
            'msds_report' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'other_documents' => ['nullable', 'array', 'max:10'],
            'other_documents.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'services.required' => 'Please select at least one service.',
            'services.*.quantity.min' => 'Quantity must be at least 1.',
            'lab_tests.required' => 'Please select at least one test.',
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            $hasServices = !empty($this->input('services'));
            $hasLabTests = !empty($this->input('lab_tests'));

            if (!$hasServices && !$hasLabTests) {
                $validator->errors()->add('lab_tests', 'Please select at least one item.');
            }

            $hasMsds = $this->hasFile('msds_report');
            $hasOther = $this->hasFile('other_documents');
            if (!$hasMsds && !$hasOther) {
                $validator->errors()->add('documents', 'Please upload at least one document (MSDS or a reference document).');
            }
        });
    }
}