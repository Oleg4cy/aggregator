<?php

namespace App\Orchid\Layouts\Shop\Edit;

use App\Orchid\Fields\Title;
use App\Orchid\Fields\SelectRelation;
use Illuminate\Database\Eloquent\Collection;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Category;
use App\Models\Shop;

class ShopCategories extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    private function createInputsGroups(
        Collection|null $categories,
        Collection|null $subCategories,
        Collection $availableCategories,
    )
    {
        $template = [
            'category' => [
                'default' => true,
                'enhanced' => true,
                'name' => 'category_id[]',
                'id' => 'select-category',
                'placeholder' => 'Выбрать тип техники',
            ],
            'subCategories' =>  [
                'multiple' => true,
                'enhanced' => true,
                'name' => 'sub_categories[]',
                'id' => 'select-subcategories',
                'title' => 'Бренды',
                'placeholder' => 'Выбрать бренды',
            ],
        ];

        if ($categories === null || $categories->isEmpty()) {
            return [$template];
        }

        $groups = [];
        foreach ($categories as $category) {
            $newCategory = [...$template['category']];
            $newCategory['current'] = $category->id;
            $newCategory['hydrated'] = true;
            $newCategory['options'] = $availableCategories->map(fn ($availableCategory) => [
                'value' => $availableCategory->id,
                'label' => $availableCategory->name,
                'selected' => (int) $availableCategory->id === (int) $category->id,
            ])->values()->all();

            $newSubCategories = [...$template['subCategories']];
            $selectedSubCategories = $subCategories?->get($category->id, collect()) ?? collect();
            $newSubCategories['current'] = implode(',', $selectedSubCategories->pluck('id')->toArray());
            $newSubCategories['hydrated'] = true;
            $selectedSubCategoryIds = $selectedSubCategories->pluck('id')->map(fn ($id) => (int) $id)->all();
            $selectedSubCategoryIdSet = array_flip($selectedSubCategoryIds);
            $availableSubCategories = $availableCategories
                ->firstWhere('id', $category->id)?->subCategories ?? collect();
            if ($availableSubCategories->isNotEmpty()) {
                $newSubCategories['default'] = true;
            }
            $newSubCategories['options'] = $availableSubCategories
                ->sort(function ($first, $second) use ($selectedSubCategoryIdSet) {
                    $firstSelected = isset($selectedSubCategoryIdSet[$first->id]);
                    $secondSelected = isset($selectedSubCategoryIdSet[$second->id]);
                    if ($firstSelected !== $secondSelected) {
                        return $firstSelected ? -1 : 1;
                    }

                    $nameComparison = strcasecmp((string) $first->name, (string) $second->name);
                    return $nameComparison !== 0 ? $nameComparison : $first->id <=> $second->id;
                })
                ->map(fn ($subCategory) => [
                    'value' => $subCategory->id,
                    'label' => $subCategory->name,
                    'selected' => isset($selectedSubCategoryIdSet[$subCategory->id]),
                ])
                ->values()
                ->all();
            $groups[] = [$newCategory, $newSubCategories];
        }

        return $groups;
    }

    public function getRow(Shop $shop): iterable
    {
        $availableCategories = Category::with('subCategories')
            ->orderBy('name')
            ->orderBy('id')
            ->get();
        $categories = null;
        $subCategories = null;
        if ($shop->id) {
            $categories = $shop->categories()
                ->orderBy('shop_category.created_at')
                ->orderBy('categories.id')
                ->get();
            $subCategories = $shop->subCategories()
                ->orderBy('shop_sub_category.created_at')
                ->orderBy('sub_categories.id')
                ->get()
                ->groupBy('category_id');
        }

        $row = [
            Title::make('Типы техники и бренды')->class('pt-4'),
            SelectRelation::make('categories')
                ->controller('categories')
                ->sorting([
                    'created_at' => 'По дате добавления',
                    'alphabetical' => 'По алфавиту',
                ], 'created_at', 'Сортировка')
                ->inputsGroups($this->createInputsGroups($categories, $subCategories, $availableCategories))->setRows(),
        ];

        return $row;
    }

    public function getMethod(): string {
        return 'categories';
    }

    protected function getSaveMethod(): string
    {
        return 'saveCategories';
    }
}
