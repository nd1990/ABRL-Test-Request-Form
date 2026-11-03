<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $setting = Setting::where('group', 'email')->where('key', 'body')->first();

        if ($setting && str_contains($setting->value, 'The quotation is valid until the date mentioned in the document.')) {
            $setting->update([
                'value' => str_replace(
                    'The quotation is valid until the date mentioned in the document.',
                    'The quotation is valid until {{valid_until}}.',
                    $setting->value
                ),
            ]);
        }
    }

    public function down(): void
    {
        $setting = Setting::where('group', 'email')->where('key', 'body')->first();

        if ($setting && str_contains($setting->value, 'The quotation is valid until {{valid_until}}.')) {
            $setting->update([
                'value' => str_replace(
                    'The quotation is valid until {{valid_until}}.',
                    'The quotation is valid until the date mentioned in the document.',
                    $setting->value
                ),
            ]);
        }
    }
};
