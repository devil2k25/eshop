<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    /**
     * The canonical settings schema, grouped by section.
     * Each entry: 'key' => ['group' => '...', 'default' => ...]
     */
    private array $schema = [
        // General
        'store_name'    => ['group' => 'general',  'default' => ''],
        'store_email'   => ['group' => 'general',  'default' => ''],
        'store_phone'   => ['group' => 'general',  'default' => ''],
        'store_address' => ['group' => 'general',  'default' => ''],
        'currency'      => ['group' => 'general',  'default' => 'USD'],
        'currency_symbol' => ['group' => 'general', 'default' => '$'],

        // Shipping
        'free_shipping_threshold' => ['group' => 'shipping', 'default' => '0'],
        'default_shipping_cost'   => ['group' => 'shipping', 'default' => '0'],

        // Tax
        'tax_rate'      => ['group' => 'tax', 'default' => '0'],
        'tax_inclusive' => ['group' => 'tax', 'default' => '0'],
    ];

    public function index(): Response
    {
        // Load all persisted settings keyed by their key column
        $persisted = Setting::all()->keyBy('key');

        $settings = [];
        foreach ($this->schema as $key => $meta) {
            $settings[$meta['group']][$key] = $persisted->has($key)
                ? $persisted[$key]->value
                : $meta['default'];
        }

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // General
            'store_name'              => 'nullable|string|max:255',
            'store_email'             => 'nullable|email|max:255',
            'store_phone'             => 'nullable|string|max:50',
            'store_address'           => 'nullable|string|max:500',
            'currency'                => 'nullable|string|max:10',
            'currency_symbol'         => 'nullable|string|max:10',

            // Shipping
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'default_shipping_cost'   => 'nullable|numeric|min:0',

            // Tax
            'tax_rate'                => 'nullable|numeric|min:0|max:100',
            'tax_inclusive'           => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $this->schema)) {
                Setting::set($key, $value ?? '', $this->schema[$key]['group']);
            }
        }

        return back()->with('success', 'Settings saved.');
    }
}
