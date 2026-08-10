<div class="menu">
    <div class="menu__inner container-wide">
        <div class="menu__equipment-types-wrapper">
            <h2 class="menu__title mb-2">Типы техники</h2>
            @include('layouts.equipment-types-list.default', [
                'equipmentTypes' => $equipmentTypes,
                'modifier' => ['menu', 'desktop'],
            ])
        </div>
        @include('layouts.search.equipment-types', [
            'equipmentTypes' => $equipmentTypes,
            'modifier' => 'menu',
            'inputID' => 'search-equipment-types',
            'inputName' => 'search-equipment-types',
            'equipmentTypeListType' => 'default',
        ])
        <div class="menu__regions-wrapper">
            <h2 class="menu__title mb-2">Регионы</h2>
            <ul class="regions-list">
                @foreach ($areas as $area)
                    <li>
                        <a href="#">
                            {{ $area->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
