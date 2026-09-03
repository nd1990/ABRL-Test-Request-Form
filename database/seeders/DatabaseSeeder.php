<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedSettings();
        $this->seedServices();
        $this->call(LabTestSeeder::class);
    }

    protected function seedAdmin(): void
    {
        if (!Admin::where('email', 'support@nexelt.com')->exists()) {
            Admin::create([
                'name' => 'Master Admin',
                'email' => 'support@nexelt.com',
                'password' => Hash::make('password'),
                'role' => 'master',
                'is_active' => true,
                'last_login_at' => null,
            ]);
        }
    }

    protected function seedSettings(): void
    {
        $settings = [
            'company' => [
                'name' => 'Your Company Pvt Ltd',
                'address' => 'Level 4, Business Tower, MG Road',
                'phone' => '+91 90000 00000',
                'email' => 'contact@yourcompany.com',
                'website' => 'https://www.yourcompany.com',
                'gst_number' => '27ABCDE1234F1Z5',
            ],
            'quotation' => [
                'prefix' => 'QT',
                'starting_number' => '1',
                'currency' => 'INR',
                'tax_percentage' => '18',
                'validity_days' => '15',
                'terms' => "1. Quotation is valid for 15 days from the date of issue.\n2. 50% advance payment is required to begin work.\n3. Prices are inclusive of applicable taxes.\n4. Delivery timeline starts after receipt of advance payment.\n5. Any changes in scope may affect the total quotation amount.",
            ],
            'payment' => [
                'bank_name' => 'State Bank of India',
                'account_name' => 'Your Company Pvt Ltd',
                'account_number' => '35678901234',
                'ifsc' => 'SBIN0001234',
                'upi' => 'yourcompany@upi',
                'instructions' => 'Please mention your quotation number as the payment reference.',
            ],
            'email' => [
                'sender_name' => 'Your Company Pvt Ltd',
                'sender_email' => 'quotes@yourcompany.com',
                'subject' => 'Quotation {{quotation_number}} from {{company_name}}',
                'body' => "Dear {{client_name}},\n\nThank you for your interest in our services.\n\nPlease find attached your quotation {{quotation_number}} dated {{date}} for a total of {{amount}}.\n\nThe quotation is valid until the date mentioned in the document.\n\nIf you have any questions, feel free to reach out to us.\n\nBest regards,\n{{company_name}}",
                'notify_new_quotation' => '1',
                'notify_new_quotation_subject' => 'New Quotation Request {{quotation_number}}',
            ],
            'pdf' => [
                'pdf_header' => 'Business Tower, MG Road, Mumbai - 400001',
                'pdf_footer' => 'Thank you for your business!',
                'signature' => '',
                'pdf_terms' => "1. Quotation is valid for 15 days from the date of issue.\n2. 50% advance payment is required to begin work.\n3. Prices are inclusive of applicable taxes.",
                'pdf_payment_details' => '',
            ],
        ];

        foreach ($settings as $group => $items) {
            foreach ($items as $key => $value) {
                if (!Setting::where('group', $group)->where('key', $key)->exists()) {
                    Setting::create([
                        'group' => $group,
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
            }
        }
    }

    protected function seedServices(): void
    {
        $services = [
            [
                'name' => 'Website Design',
                'category' => 'Web Development',
                'description' => 'Custom, responsive website design tailored to your brand with a modern, professional look.',
                'price' => 15000.00,
                'tax_percentage' => 18.00,
                'unit' => 'page',
                'icon' => '🎨',
                'sort_order' => 1,
            ],
            [
                'name' => 'Website Development',
                'category' => 'Web Development',
                'description' => 'Full-stack development of your website including backend, CMS and deployment.',
                'price' => 45000.00,
                'tax_percentage' => 18.00,
                'unit' => 'project',
                'icon' => '💻',
                'sort_order' => 2,
            ],
            [
                'name' => 'Mobile App Development',
                'category' => 'Mobile',
                'description' => 'Native and cross-platform mobile application development for Android and iOS.',
                'price' => 120000.00,
                'tax_percentage' => 18.00,
                'unit' => 'project',
                'icon' => '📱',
                'sort_order' => 3,
            ],
            [
                'name' => 'SEO & Digital Marketing',
                'category' => 'Marketing',
                'description' => 'Search engine optimization and digital marketing to grow your online presence.',
                'price' => 15000.00,
                'tax_percentage' => 18.00,
                'unit' => 'month',
                'icon' => '📈',
                'sort_order' => 4,
            ],
            [
                'name' => 'Logo & Branding',
                'category' => 'Design',
                'description' => 'Professional logo design and complete brand identity package.',
                'price' => 8000.00,
                'tax_percentage' => 18.00,
                'unit' => 'package',
                'icon' => '✨',
                'sort_order' => 5,
            ],
            [
                'name' => 'Technical Support',
                'category' => 'Support',
                'description' => 'Ongoing technical maintenance and support for your website or application.',
                'price' => 5000.00,
                'tax_percentage' => 18.00,
                'unit' => 'month',
                'icon' => '🛠️',
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service + ['is_active' => true]);
        }
    }
}