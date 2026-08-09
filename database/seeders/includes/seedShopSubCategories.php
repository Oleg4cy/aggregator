<?php
function seedShopSubCategories(): void
{
    $shops = \App\Models\Shop::with('categories')->get();
    foreach ($shops as $shop) {
        foreach ($shop->categories as $category) {
            $brands = \App\Models\SubCategory::where('category_id', $category->id);
            $brandCount = $brands->count();
            if ($brandCount === 0) {
                continue;
            }

            $subCategoryIds = $brands
                ->inRandomOrder()
                ->limit(rand(1, min(5, $brandCount)))
                ->pluck('id')
                ->toArray();

            if (!empty($subCategoryIds)) {
                $shop->subcategories()->syncWithoutDetaching($subCategoryIds);
            }
        }
    }
}
