<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public function index(): Response
    {
        $user = auth()->user()->load('addresses');

        $recentOrders = auth()->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Shop/Profile/Index', [
            'user'         => $user->only(['id', 'name', 'email', 'phone', 'avatar']),
            'addresses'    => $user->addresses->sortByDesc('is_default')->values(),
            'recentOrders' => $recentOrders,
        ]);
    }

    public function addresses(): Response
    {
        $addresses = auth()->user()
            ->addresses()
            ->orderByDesc('is_default')
            ->get();

        return Inertia::render('Shop/Profile/Addresses', compact('addresses'));
    }

    // ─── Profile Actions ──────────────────────────────────────────────────────

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'  => ['nullable', 'string', 'max:50'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $data = $request->only(['name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if stored on disk
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }

    // ─── Address Actions ──────────────────────────────────────────────────────

    public function storeAddress(Request $request): RedirectResponse
    {
        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:50'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:100'],
            'state'         => ['required', 'string', 'max:100'],
            'zip_code'      => ['required', 'string', 'max:20'],
            'country'       => ['required', 'string', 'max:100'],
        ]);

        $user = auth()->user();

        // If this is the user's first address, make it the default automatically
        $isFirst = $user->addresses()->doesntExist();

        $user->addresses()->create(array_merge(
            $request->only(['name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'zip_code', 'country']),
            ['is_default' => $isFirst]
        ));

        return back()->with('success', 'Address added successfully.');
    }

    public function updateAddress(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:50'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:100'],
            'state'         => ['required', 'string', 'max:100'],
            'zip_code'      => ['required', 'string', 'max:20'],
            'country'       => ['required', 'string', 'max:100'],
        ]);

        $address->update(
            $request->only(['name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'zip_code', 'country'])
        );

        return back()->with('success', 'Address updated successfully.');
    }

    public function deleteAddress(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $wasDefault = $address->is_default;

        $address->delete();

        // If the deleted address was the default, promote the next available address
        if ($wasDefault) {
            $next = auth()->user()->addresses()->first();
            $next?->update(['is_default' => true]);
        }

        return back()->with('success', 'Address deleted.');
    }

    public function setDefaultAddress(Address $address): RedirectResponse
    {
        abort_unless($address->user_id === auth()->id(), 403);

        // Unset all current defaults for this user, then set the chosen one
        auth()->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated.');
    }
}
