<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title'       => 'Summer Sale Up to 50% Off',
                'subtitle'    => 'Shop the latest trends at unbeatable prices',
                'image'       => 'banners/banner1.jpg',
                'button_text' => 'Shop Now',
                'button_link' => '/products',
                'position'    => 'home',
                'is_active'   => true,
                'sort_order'  => 1,
            ],
            [
                'title'       => 'New Arrivals',
                'subtitle'    => 'Discover our newest collection',
                'image'       => 'banners/banner2.jpg',
                'button_text' => 'Explore',
                'button_link' => '/products?sort=newest',
                'position'    => 'home',
                'is_active'   => true,
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Free Shipping on Orders Over $100',
                'subtitle'    => 'Shop more, save more!',
                'image'       => 'banners/banner3.jpg',
                'button_text' => 'Shop Now',
                'button_link' => '/products',
                'position'    => 'home',
                'is_active'   => true,
                'sort_order'  => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
