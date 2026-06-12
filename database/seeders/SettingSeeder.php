<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // General settings
        Setting::set('store_name',    'ShopVue',                              'general');
        Setting::set('store_email',   'support@shopvue.com',                  'general');
        Setting::set('store_phone',   '+1 (555) 000-0000',                    'general');
        Setting::set('store_address', '123 Commerce St, New York, NY 10001',  'general');
        Setting::set('currency',      'USD',                                   'general');
        Setting::set('currency_symbol', '$',                                   'general');

        // Shipping settings
        Setting::set('free_shipping_threshold', '100',  'shipping');
        Setting::set('default_shipping_cost',   '9.99', 'shipping');

        // Tax settings
        Setting::set('tax_rate',      '8.5',   'tax');
        Setting::set('tax_inclusive', 'false',  'tax');
    }
}
