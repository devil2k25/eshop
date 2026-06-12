<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders', 'total');

        if ($request->search) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
            );
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $customers = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters'   => $request->only(['search', 'is_active']),
        ]);
    }

    public function show(User $user): Response
    {
        abort_if($user->is_admin, 404);

        $user->load([
            'orders' => fn($q) => $q->latest()->take(10),
            'addresses',
            'reviews' => fn($q) => $q->with('product')->latest()->take(10),
        ]);

        $user->loadCount('orders');
        $user->loadSum('orders', 'total');

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $user,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);
        $user->update($request->only(['is_active']));
        return back()->with('success', 'Customer updated.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->is_admin, 403);

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Customer account {$status}.");
    }
}
