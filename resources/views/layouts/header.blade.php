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
                <x-picture name="logo.png" path='assets/img/' alt='' />
                <x-picture classname="header__top-logo--text" name="textlogo.png" path='assets/img/'
                    alt='RentSell logo' />
            </a>
            <p class="header__top-text mr-auto">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit,
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
            'categories' => \App\Models\Category::all(),
            'areas' => \App\Models\Area::where(
                'city_id',
                \App\Http\Controllers\LocationController::getCityID())->get(),
        ])
    </div>
</header>
