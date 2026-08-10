<aside class="aside">
    <div class="aside__collapsed">
        <button class="btn aside__btn filter-toggle-btn">
            <x-icon-filter-icon />
        </button>
        <button class="btn aside__btn">
            <x-icon-places-icon />
            <span class="aside__btn--span">5</span>
        </button>
        <button class="btn aside__btn">
            <x-icon-location-icon width="30" height="30"/>
            <span class="aside__btn--span">5</span>
        </button>
        <button class="btn aside__btn">
            <x-icon-metro-icon />
            <span class="aside__btn--span">5</span>
        </button>
        <div class="aside__rating">
            <x-icon-star-full width="18" height="18" viewBox="0 0 18 18"/>
            <p>3.0</p>
        </div>
        <button class="btn aside__btn"><x-icon-clock-icon width="30" height="30"/></button>
        <button class="btn aside__btn"><x-icon-calculator-icon width="30" height="30"/></button>
    </div>

    <div class="aside__expanded">
        <div class="flex-btw">
            <button class="btn aside__collapse-btn">
                <span> Фильтр </span>
            </button>

            <button id="filter-clear-all" class="btn aside__clear-btn">
                <x-icon-trash />
                <span>Сбросить</span>
            </button>
        </div>

        <div class="mb-3">
            <p class="aside__label mt-3 mb-15">Типы техники</p>
            <div id="filter-select-1">
                <button id="toggle-equipment-type" type="button" class="btn aside__equipment-type-btn" name="city" value="spb" data-select="toggle" data-index="0">
                    Типы техники
                </button>
            </div>

            {{ app(\App\Services\FilterService::class)->getFilterByName('location')->render(null, false) }}
        </div>

        {{ app(\App\Services\FilterService::class)->getFilterByName('rating')->render(null, false) }}
        {{ app(\App\Services\FilterService::class)->getFilterByName('options')->render(null, false) }}
    </div>
</aside>
{{ app(\App\Services\FilterService::class)->getFilterByName('equipment-types')->render(null, false) }}

