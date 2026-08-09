<?php

function seedEquipmentTypesAndBrands(): void
{
    $equipmentTypes = [
        ['Смартфоны', 'по ремонту смартфонов', ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Honor', 'realme', 'OPPO', 'vivo', 'Google']],
        ['Ноутбуки', 'по ремонту ноутбуков', ['Apple', 'ASUS', 'Acer', 'Lenovo', 'HP', 'Dell', 'MSI', 'Huawei', 'Honor']],
        ['Планшеты', 'по ремонту планшетов', ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Lenovo']],
        ['Умные часы', 'по ремонту умных часов', ['Apple', 'Samsung', 'Huawei', 'Xiaomi', 'Garmin', 'Amazfit']],
        ['Телевизоры', 'по ремонту телевизоров', ['Samsung', 'LG', 'Sony', 'Philips', 'Xiaomi', 'TCL', 'Hisense']],
        ['Игровые приставки', 'по ремонту игровых приставок', ['Sony PlayStation', 'Microsoft Xbox', 'Nintendo']],
        ['Наушники и аудиотехника', 'по ремонту наушников и аудиотехники', ['Apple', 'Samsung', 'Sony', 'JBL', 'Marshall', 'Bose', 'Xiaomi']],
        ['Мониторы', 'по ремонту мониторов', ['Samsung', 'LG', 'ASUS', 'Acer', 'Dell', 'MSI', 'AOC', 'BenQ']],
    ];

    foreach ($equipmentTypes as [$name, $nameForTitle, $brands]) {
        $equipmentType = \App\Models\EquipmentType::firstOrNew(['name' => $name]);
        $equipmentType->name = $name;
        $equipmentType->name_for_title = $nameForTitle;
        $equipmentType->save();

        foreach ($brands as $brandName) {
            $brand = \App\Models\Brand::firstOrNew([
                'name' => $brandName,
                'equipment_type_id' => $equipmentType->id,
            ]);
            $brand->name = $brandName;
            $brand->equipment_type_id = $equipmentType->id;
            $brand->save();
        }
    }
}
