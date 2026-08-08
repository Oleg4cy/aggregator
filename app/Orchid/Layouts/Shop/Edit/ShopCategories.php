<?php

namespace App\Orchid\Layouts\Shop\Edit;

use App\Orchid\Fields\Title;
use App\Orchid\Fields\SelectRelation;
use Illuminate\Database\Eloquent\Collection;
use App\Orchid\Layouts\Shop\Edit\ShopEditRow;
use App\Models\Shop;

class ShopCategories extends ShopEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    private function createInputsGroups(Collection|null $categories, Collection|null $subCategories)
    {
        $template = [
            'category' => [
                'default' => true,
                'name' => 'category_id[]',
                'id' => 'select-category',
                'placeholder' => 'Выбрать категорию',
            ],
            'subCategories' =>  [
                'multiple' => true,
                'name' => 'sub_categories[]',
                'id' => 'select-subcategories',
                'title' => 'Подкатегории',
                'placeholder' => 'Выбрать подкатегории',
            ],
        ];

        if ($categories === null || $categories->isEmpty()) {
            return [$template];
        }

        $groups = [];
        foreach ($categories as $category) {
            $newCategory = [...$template['category']];
            $newCategory['current'] = $category->id;
            $newSubCategories = [...$template['subCategories']];
            $selectedSubCategories = $subCategories?->get($category->id, collect());
            $newSubCategories['current'] = implode(',', $selectedSubCategories->pluck('id')->toArray());
            $groups[] = [$newCategory, $newSubCategories];
        }

        return $groups;
    }

    public function getRow(Shop $shop): iterable
    {
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
            Title::make('Категории')->class('pt-4'),
            SelectRelation::make('categories')
                ->controller('categories')
                ->inputsGroups($this->createInputsGroups($categories, $subCategories))->setRows(),
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
