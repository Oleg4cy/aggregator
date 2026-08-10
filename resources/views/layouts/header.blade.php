<header class="header">
    <div class="container-wide">
        <div class="header__top">
            <button id="burger" class="btn header__menu-btn">
                <span class='header__menu-lines'>
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
            <a class="header__logo ml-4" href="/">
                <span class="header__logo-text">Сервисные центры</span>
            </a>
            <p class="header__top-text mr-auto">
                Поиск сервисных центров по ремонту электроники
            </p>

            <div class="header-city mr-15">
                {{ app(\App\Services\FilterService::class)->getFilterByName('city')->render(null, false) }}
                <div id="city_confirm_popup" class="header-city-popup hidden">
                    <x-close-btn id="city_popup_close" class="header-city-popup__close" />
                    <p class="header-city-popup__label">Это ваш город?</p>
                    <button id="city_confirm_true"
                        class="btn btn--primary header-city-popup__confirm">Подтвердить</button>
                </div>
            </div>
            <a class="btn btn--primary header__top-link" href="#">Добавить сервисный центр</a>
            {{-- <button class="btn header__map-btn">
                <x-icon-location-icon />
            </button> --}}
        </div>
        @include('layouts.menu', [
            'equipmentTypes' => \App\Models\EquipmentType::all(),
            'areas' => \App\Models\Area::where(
                'city_id',
                \App\Http\Controllers\LocationController::getCityID())->get(),
        ])
    </div>
</header>
