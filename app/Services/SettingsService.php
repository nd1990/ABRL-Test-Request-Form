<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public function get(string $group, string $key, $default = null): ?string
    {
        return Setting::get($group, $key, $default);
    }

    public function group(string $group): array
    {
        return Setting::getGroup($group);
    }

    public function saveGroup(string $group, array $data, ?\App\Models\Admin $admin = null): void
    {
        foreach ($data as $key => $value) {
            Setting::set($group, $key, $value);
        }
    }

    public function company(): array
    {
        return $this->group('company');
    }

    public function quotation(): array
    {
        return $this->group('quotation');
    }

    public function payment(): array
    {
        return $this->group('payment');
    }

    public function email(): array
    {
        return $this->group('email');
    }

    public function pdf(): array
    {
        return $this->group('pdf');
    }

    public function currencySymbol(): string
    {
        $map = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£'];

        return $map[$this->quotation()['currency'] ?? 'INR'] ?? '₹';
    }

    public function terms(): string
    {
        return $this->get('quotation', 'terms', '');
    }
}
