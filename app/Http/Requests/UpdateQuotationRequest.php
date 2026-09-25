<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_name' => ['required', 'string', 'max:191'],
            'company_name' => ['nullable', 'string', 'max:191'],
            'sample_name' => ['nullable', 'string', 'max:191'],
            'sample_batch_no' => ['nullable', 'string', 'max:191'],
            'sample_physical_form' => ['nullable', 'string', 'max:191'],
            'sample_storage_condition' => ['nullable', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'address_line2' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:191'],
            'state' => ['nullable', 'string', 'max:191'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:191'],
            'gst_number' => ['nullable', 'string', 'max:50'],
            'courier_address' => ['nullable', 'string', 'max:500'],
            'courier_city' => ['nullable', 'string', 'max:191'],
            'courier_state' => ['nullable', 'string', 'max:191'],
            'courier_postal_code' => ['nullable', 'string', 'max:20'],
            'courier_country' => ['nullable', 'string', 'max:191'],
            'quotation_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,sent,accepted,rejected,cancelled'],
            'services' => ['sometimes', 'array', 'min:1', 'max:5'],
            'services.*.service_id' => ['required_with:services', 'integer'],
            'services.*.quantity' => ['required_with:services', 'integer', 'min:1', 'max:9999'],
            'services.*.discount' => ['nullable', 'numeric', 'min:0'],
            'services.*.notes' => ['nullable', 'string', 'max:1000'],
            'lab_tests' => ['sometimes', 'array', 'min:1', 'max:5'],
            'lab_tests.*.lab_test_id' => ['required_with:lab_tests', 'integer'],
            'lab_tests.*.no_of_samples' => ['required_with:lab_tests', 'integer', 'min:1', 'max:9999'],
            'lab_tests.*.notes' => ['nullable', 'string', 'max:1000'],
            'project_description' => ['nullable', 'string', 'max:5000'],
            'additional_requirements' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'expected_timeline' => ['nullable', 'string', 'max:191'],
            'preferred_contact_method' => ['nullable', 'string', 'max:191'],
        ];
    }
}