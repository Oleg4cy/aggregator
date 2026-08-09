<div class="menu">
    <div class="menu__inner container-wide">
        <div class="menu__categories-wrapper">
            <h2 class="menu__title mb-2">Типы техники</h2>
            @include('layouts.categories-list.default', [
                'categories' => $categories,
                'modifier' => ['menu', 'desktop'],
            ])
        </div>
        @include('layouts.search.categories', [
            'categories' => $categories,
            'modifier' => 'menu',
            'inputID' => 'search-categories',
            'inputName' => 'search-categories',
            'categoriesListType' => 'default',
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

        <ul class="menu__promos">
            @for ($i = 0; $i < 3; $i++)
                <li>
                    <a href="#">
                        <img src="{{ asset('assets/img/categories/sale/1.jpg') }}" alt="Promo banner" />
                    </a>
                </li>
            @endfor
        </ul>
    </div>
</div>
