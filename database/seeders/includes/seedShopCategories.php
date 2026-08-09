<?php
function seedShopCategories(): void
{
    $shops = \App\Models\Shop::all();
    foreach ($shops as $shop) {
        $categoryCount = \App\Models\Category::count();
        if ($categoryCount === 0) {
            continue;
        }

        $ids = \App\Models\Category::inRandomOrder()
            ->limit(rand(1, min(4, $categoryCount)))
            ->pluck('id');
        $ids && $shop->categories()->syncWithoutDetaching($ids);
    }
}
