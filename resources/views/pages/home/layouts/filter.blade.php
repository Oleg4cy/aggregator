<section class="filter">
    <div class="filter__container">
        <aside class="filter__aside">
            <div class="filter__results" data-service-center-results>
                @include('layouts.service-center-list', ['serviceCenters' => $serviceCenters])
            </div>
            <button
                type="button"
                class="filter__mobile-map-toggle"
                data-mobile-map-toggle
                aria-label="Показать карту"
                aria-pressed="false"
            >
                <x-icon-slider-arrow-left aria-hidden="true" />
            </button>
        </aside>
        <div class="filter__controls">
            <button
                type="button"
                class="filter__filter-toggle"
                data-filter-panel-toggle
                aria-expanded="false"
                aria-controls="service-center-filters-panel"
            >
                <x-icon-filter-icon aria-hidden="true" />
                <span>Фильтры</span>
            </button>
            <a
                class="btn btn--primary btn--primary-hover-active filter__add-service-center"
                href="#"
                aria-label="Добавить сервисный центр"
                title="Добавить сервисный центр"
            >
                <span class="filter__add-service-center-icon" aria-hidden="true"></span>
                <span>Добавить</span>
            </a>
        </div>
        <section
            id="service-center-filters-panel"
            class="filter__filters-panel"
            data-filter-panel
            aria-hidden="true"
        >
            <header class="filter__filters-header">
                <h2 class="filter__filters-title">
                    Фильтры
                </h2>

                <button
                    id="filter-clear-all"
                    type="button"
                    class="filter__filters-reset"
                >
                    Сбросить
                </button>

                <button
                    type="button"
                    class="filter__filters-close"
                    data-filter-panel-close
                    aria-label="Закрыть фильтры"
                    title="Закрыть"
                >
                    <x-icon-close aria-hidden="true" />
                </button>
            </header>

            <div class="filter__filters-body">
                <section class="filter__filters-section">
                    {{ app(\App\Services\FilterService::class)
                        ->getFilterByName('rating')
                        ->render($cityID) }}
                </section>

                <section class="filter__filters-section">
                    <h3 class="filter__filters-section-title">
                        Расположение
                    </h3>

                    {{ app(\App\Services\FilterService::class)
                        ->getFilterByName('location')
                        ->render($cityID) }}
                </section>

                <section class="filter__filters-section">
                    <h3 class="filter__filters-section-title">
                        Типы техники и бренды
                    </h3>

                    {{ app(\App\Services\FilterService::class)
                        ->getFilterByName('equipment-types')
                        ->render($cityID) }}
                </section>

                <section class="filter__filters-section">
                    <h3 class="filter__filters-section-title">
                        Дополнительные параметры
                    </h3>

                    {{ app(\App\Services\FilterService::class)
                        ->getFilterByName('options')
                        ->render($cityID) }}
                </section>
            </div>
        </section>
        <div id="filter-map" class="filter__map"></div>
        <div class="filter__details" data-service-center-details hidden></div>
    </div>
</section>
