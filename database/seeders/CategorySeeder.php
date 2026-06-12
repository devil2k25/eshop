<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            [
                'name'        => 'Electronics',
                'slug'        => 'electronics',
                'description' => 'Latest gadgets and electronic devices for everyday use.',
                'is_active'   => true,
                'sort_order'  => 1,
                'children'    => [
                    'Smartphones',
                    'Laptops',
                    'Headphones',
                    'Cameras',
                ],
            ],
            [
                'name'        => 'Fashion',
                'slug'        => 'fashion',
                'description' => 'Trendy clothing, footwear, and accessories for every style.',
                'is_active'   => true,
                'sort_order'  => 2,
                'children'    => [
                    "Men's Clothing",
                    "Women's Clothing",
                    'Shoes',
                    'Bags & Accessories',
                ],
            ],
            [
                'name'        => 'Home & Living',
                'slug'        => 'home-living',
                'description' => 'Everything you need to make your home beautiful and comfortable.',
                'is_active'   => true,
                'sort_order'  => 3,
                'children'    => [
                    'Furniture',
                    'Kitchen',
                    'Bedding',
                    'Decor',
                ],
            ],
            [
                'name'        => 'Sports & Outdoors',
                'slug'        => 'sports-outdoors',
                'description' => 'Gear and equipment for sports enthusiasts and outdoor adventurers.',
                'is_active'   => true,
                'sort_order'  => 4,
                'children'    => [],
            ],
            [
                'name'        => 'Books & Stationery',
                'slug'        => 'books-stationery',
                'description' => 'Books, notebooks, pens, and all your stationery needs.',
                'is_active'   => true,
                'sort_order'  => 5,
                'children'    => [],
            ],
        ];

        foreach ($parents as $parentData) {
            $children = $parentData['children'];
            unset($parentData['children']);

            $parent = Category::create($parentData);

            foreach ($children as $index => $childName) {
                Category::create([
                    'name'        => $childName,
                    'slug'        => Str::slug($childName),
                    'description' => $childName . ' — browse our full selection.',
                    'is_active'   => true,
                    'sort_order'  => $index + 1,
                    'parent_id'   => $parent->id,
                ]);
            }
        }
    }
}
