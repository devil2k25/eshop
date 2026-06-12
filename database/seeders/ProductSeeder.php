<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker       = \Faker\Factory::create();
        $categoryIds = Category::pluck('id')->toArray();
        $brandIds    = Brand::pluck('id')->toArray();

        for ($i = 1; $i <= 30; $i++) {
            $name  = $faker->unique()->words(3, true);
            $price = round($faker->randomFloat(2, 19.99, 999.99), 2);

            // ~30% chance of being featured
            $isFeatured = $faker->boolean(30);

            // First 10 products are "new"
            $isNew = $i <= 10;

            // ~70% of products have a compare price
            $comparePrice = $faker->boolean(70)
                ? round($price * 1.2, 2)
                : null;

            $product = Product::create([
                'name'                => ucwords($name),
                'slug'                => Str::slug($name) . '-' . $i,
                'short_description'   => $faker->sentence(),
                'description'         => $faker->paragraph(),
                'sku'                 => 'SKU-' . strtoupper(uniqid()),
                'price'               => $price,
                'compare_price'       => $comparePrice,
                'stock'               => $faker->numberBetween(0, 100),
                'low_stock_threshold' => 5,
                'category_id'         => $faker->randomElement($categoryIds),
                'brand_id'            => $faker->randomElement($brandIds),
                'is_active'           => true,
                'is_featured'         => $isFeatured,
                'is_new'              => $isNew,
                'avg_rating'          => round($faker->randomFloat(1, 3.5, 5.0), 1),
                'review_count'        => $faker->numberBetween(0, 50),
            ]);

            // Create 1–3 product images per product
            $imageCount = $faker->numberBetween(1, 3);
            for ($j = 1; $j <= $imageCount; $j++) {
                $n = $faker->numberBetween(1, 10);
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => "products/placeholder-{$n}.jpg",
                    'alt'        => $product->name,
                    'is_primary' => $j === 1,
                    'sort_order' => $j,
                ]);
            }
        }
    }
}
