<?php

namespace App\Orchid\Layouts\ServiceCenter\Edit;

use App\Orchid\Fields\Title;
use App\Orchid\Fields\SelectRelation;
use Illuminate\Database\Eloquent\Collection;
use App\Orchid\Layouts\ServiceCenter\Edit\ServiceCenterEditRow;
use App\Models\EquipmentType;
use App\Models\ServiceCenter;

class ServiceCenterEquipmentTypesAndBrands extends ServiceCenterEditRow
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    private function createInputsGroups(
        Collection|null $equipmentTypes,
        Collection|null $brands,
        Collection $availableEquipmentTypes,
    )
    {
        $template = [
            'equipmentType' => [
                'default' => true,
                'enhanced' => true,
                'name' => 'equipment_type_id[]',
                'id' => 'select-category',
                'title' => 'Тип техники',
                'placeholder' => 'Выбрать тип техники',
            ],
            'brands' =>  [
                'multiple' => true,
                'enhanced' => true,
                'name' => 'brand_id[]',
                'id' => 'select-subcategories',
                'title' => 'Бренды',
                'placeholder' => 'Выбрать бренды',
            ],
        ];

        if ($equipmentTypes === null || $equipmentTypes->isEmpty()) {
            return [$template];
        }

        $groups = [];
        foreach ($equipmentTypes as $equipmentType) {
            $newEquipmentType = [...$template['equipmentType']];
            $newEquipmentType['current'] = $equipmentType->id;
            $newEquipmentType['hydrated'] = true;
            $newEquipmentType['options'] = $availableEquipmentTypes->map(fn ($availableEquipmentType) => [
                'value' => $availableEquipmentType->id,
                'label' => $availableEquipmentType->name,
                'selected' => (int) $availableEquipmentType->id === (int) $equipmentType->id,
            ])->values()->all();

            $newBrands = [...$template['brands']];
            $selectedBrands = $brands?->get($equipmentType->id, collect()) ?? collect();
            $newBrands['current'] = implode(',', $selectedBrands->pluck('id')->toArray());
            $newBrands['hydrated'] = true;
            $selectedBrandIds = $selectedBrands->pluck('id')->map(fn ($id) => (int) $id)->all();
            $selectedBrandIdSet = array_flip($selectedBrandIds);
            $availableBrands = $availableEquipmentTypes
                ->firstWhere('id', $equipmentType->id)?->brands ?? collect();
            if ($availableBrands->isNotEmpty()) {
                $newBrands['default'] = true;
            }
            $newBrands['options'] = $availableBrands
                ->sort(function ($first, $second) use ($selectedBrandIdSet) {
                    $firstSelected = isset($selectedBrandIdSet[$first->id]);
                    $secondSelected = isset($selectedBrandIdSet[$second->id]);
                    if ($firstSelected !== $secondSelected) {
                        return $firstSelected ? -1 : 1;
                    }

                    $nameComparison = strcasecmp((string) $first->name, (string) $second->name);
                    return $nameComparison !== 0 ? $nameComparison : $first->id <=> $second->id;
                })
                ->map(fn ($brand) => [
                    'value' => $brand->id,
                    'label' => $brand->name,
                    'selected' => isset($selectedBrandIdSet[$brand->id]),
                ])
                ->values()
                ->all();
            $groups[] = [$newEquipmentType, $newBrands];
        }

        return $groups;
    }

    public function getRow(ServiceCenter $serviceCenter): iterable
    {
        $availableEquipmentTypes = EquipmentType::with('brands')
            ->orderBy('name')
            ->orderBy('id')
            ->get();
        $equipmentTypes = null;
        $brands = null;
        if ($serviceCenter->id) {
            $equipmentTypes = $serviceCenter->equipmentTypes()
                ->orderBy('service_center_equipment_type.created_at')
                ->orderBy('equipment_types.id')
                ->get();
            $brands = $serviceCenter->brands()
                ->orderBy('service_center_brand.created_at')
                ->orderBy('brands.id')
                ->get()
                ->groupBy('equipment_type_id');
        }

        $row = [
            Title::make('Типы техники и бренды')->class('pt-4'),
            SelectRelation::make('categories')
                ->controller('categories')
                ->sorting([
                    'created_at' => 'По дате добавления',
                    'alphabetical' => 'По алфавиту',
                ], 'created_at', 'Сортировка')
                ->inputsGroups($this->createInputsGroups($equipmentTypes, $brands, $availableEquipmentTypes))->setRows(),
        ];

        return $row;
    }

    public function getMethod(): string {
        return 'equipmentTypesAndBrands';
    }

    protected function getSaveMethod(): string
    {
        return 'saveEquipmentTypesAndBrands';
    }
}
