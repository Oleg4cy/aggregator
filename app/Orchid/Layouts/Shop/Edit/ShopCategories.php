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

    private function createInputsGroups(Collection|null $categories)
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
        foreach ($categories as $key => $category) {
            $newCategory = [...$template['category']];
            $newCategory['current'] = $key;
            $newSubCategories = [...$template['subCategories']];
            $newSubCategories['current'] = implode(',', $category->pluck('id')->toArray());
            $groups[] = [$newCategory, $newSubCategories];
        }

        return $groups;
    }

    public function getRow(Shop $shop): iterable
    {
        $categories = null;
        if ($shop->id) {
            $categories = \App\Models\SubCategory::getByShopID($shop->id)->get()->groupBy('category_id');
        }

        $row = [
            Title::make('Категории')->class('pt-4'),
            SelectRelation::make('categories')
                ->controller('categories')
                ->inputsGroups($this->createInputsGroups($categories))->setRows(),
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
