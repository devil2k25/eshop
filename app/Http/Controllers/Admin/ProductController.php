<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Product::with(['category', 'brand', 'primaryImage'])
            ->withTrashed();

        if ($request->search) {
            $query->where(fn($q) => $q
                ->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%")
            );
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->status === 'low_stock') {
            $query->whereColumn('stock', '<=', 'low_stock_threshold');
        } elseif ($request->status === 'deleted') {
            $query->onlyTrashed();
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products'   => $products,
            'categories' => Category::where('is_active', true)->get(['id', 'name']),
            'brands'     => Brand::where('is_active', true)->get(['id', 'name']),
            'filters'    => $request->only(['search', 'category_id', 'brand_id', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => Category::where('is_active', true)->get(['id', 'name', 'parent_id']),
            'brands'     => Brand::where('is_active', true)->get(['id', 'name']),
            'tags'       => Tag::all(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'sku'                => 'nullable|string|unique:products',
            'price'              => 'required|numeric|min:0',
            'compare_price'      => 'nullable|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'category_id'        => 'nullable|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'weight'             => 'nullable|numeric|min:0',
            'dimensions'         => 'nullable|string',
            'is_active'          => 'boolean',
            'is_featured'        => 'boolean',
            'is_new'             => 'boolean',
            'track_inventory'    => 'boolean',
            'allow_backorder'    => 'boolean',
            'has_variants'       => 'boolean',
            'meta_title'         => 'nullable|string',
            'meta_description'   => 'nullable|string',
            'tags'               => 'nullable|array',
            'images'             => 'nullable|array',
            'images.*'           => 'image|max:2048',
            'variants'           => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $product = Product::create($validated);

        if (!empty($validated['tags'])) {
            $product->tags()->sync($validated['tags']);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image'      => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        if (!empty($request->variants)) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        $product->load(['images', 'variants', 'tags', 'category', 'brand']);

        return Inertia::render('Admin/Products/Form', [
            'product'    => $product,
            'categories' => Category::where('is_active', true)->get(['id', 'name', 'parent_id']),
            'brands'     => Brand::where('is_active', true)->get(['id', 'name']),
            'tags'       => Tag::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'short_description'  => 'nullable|string',
            'description'        => 'nullable|string',
            'sku'                => 'nullable|string|unique:products,sku,' . $product->id,
            'price'              => 'required|numeric|min:0',
            'compare_price'      => 'nullable|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'category_id'        => 'nullable|exists:categories,id',
            'brand_id'           => 'nullable|exists:brands,id',
            'weight'             => 'nullable|numeric|min:0',
            'dimensions'         => 'nullable|string',
            'is_active'          => 'boolean',
            'is_featured'        => 'boolean',
            'is_new'             => 'boolean',
            'track_inventory'    => 'boolean',
            'allow_backorder'    => 'boolean',
            'has_variants'       => 'boolean',
            'meta_title'         => 'nullable|string',
            'meta_description'   => 'nullable|string',
            'tags'               => 'nullable|array',
            'images'             => 'nullable|array',
            'images.*'           => 'image|max:2048',
            'variants'           => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $product->update($validated);

        if (isset($validated['tags'])) {
            $product->tags()->sync($validated['tags']);
        }

        if ($request->hasFile('images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'image'      => $path,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function restore(int $id): RedirectResponse
    {
        Product::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Product restored.');
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();

        return back()->with('success', 'Image deleted.');
    }

    public function setPrimaryImage(ProductImage $image): RedirectResponse
    {
        ProductImage::where('product_id', $image->product_id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image set.');
    }
}
