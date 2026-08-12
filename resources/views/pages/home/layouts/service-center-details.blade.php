<div class="service-center-details">
    <button
        type="button"
        class="service-center-details__back"
        data-service-center-details-back
        aria-label="Назад к списку"
        title="Назад к списку"
    >
        <x-icon-arrow-square-left aria-hidden="true" />
    </button>

    @if (filled(trim((string) $serviceCenter->logo)))
        <div class="service-center-details__logo">
            <img
                src="{{ $serviceCenter->logo . 'id/' . rand(1, 500) }}/100/100"
                alt="{{ $serviceCenter->name }}"
            />
        </div>
    @endif

    <h2 class="service-center-details__title">
        {{ $serviceCenter->name }}
    </h2>

    <x-display-rating rating="{{ $serviceCenter->average_rating }}" />

    @if ($status = \App\Services\TitleService::timeBeforeClose($serviceCenter))
        <p class="service-center-details__time">{!! $status !!}</p>
    @endif

    @if (filled(trim((string) $serviceCenter->address)))
        <p class="service-center-details__address">
            {{ $serviceCenter->address }}
        </p>
    @endif

    <nav
        class="service-center-details__tabs"
        aria-label="Разделы сервисного центра"
    >
        <button
            type="button"
            class="service-center-details__tab active"
            data-tab-path="service-center-overview-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            aria-selected="true"
        >
            Обзор
        </button>
        <button
            type="button"
            class="service-center-details__tab"
            data-tab-path="service-center-services-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            aria-selected="false"
        >
            Услуги
        </button>
        @if ($serviceCenter->buyback)
            <button
                type="button"
                class="service-center-details__tab"
                data-tab-path="service-center-buyback-{{ $serviceCenter->id }}"
                data-tab-group="service-center-main-{{ $serviceCenter->id }}"
                aria-selected="false"
            >
                Выкуп
            </button>
        @endif
        <button
            type="button"
            class="service-center-details__tab"
            data-tab-path="service-center-reviews-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            aria-selected="false"
        >
            Отзывы
        </button>
        <button
            type="button"
            class="service-center-details__tab"
            data-tab-path="service-center-photos-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            aria-selected="false"
        >
            Фото
        </button>
        <button
            type="button"
            class="service-center-details__tab"
            data-tab-path="service-center-contacts-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            aria-selected="false"
        >
            Контакты
        </button>
    </nav>

    <div class="service-center-details__panels">
        <section
            class="service-center-details__panel open"
            data-tab-target="service-center-overview-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        >
            @if (filled(trim((string) $serviceCenter->description)))
                <p class="service-center-details__description">
                    {{ $serviceCenter->description }}
                </p>
            @endif
        </section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-services-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        ></section>

        @if ($serviceCenter->buyback)
            <section
                class="service-center-details__panel"
                data-tab-target="service-center-buyback-{{ $serviceCenter->id }}"
                data-tab-group="service-center-main-{{ $serviceCenter->id }}"
            ></section>
        @endif

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-reviews-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        >
            @if ($serviceCenter->reviewSources->isNotEmpty())
                <nav
                    class="service-center-details__subtabs"
                    aria-label="Источники отзывов"
                >
                    @foreach ($serviceCenter->reviewSources as $reviewSource)
                        <button
                            type="button"
                            class="service-center-details__subtab{{ $loop->first ? ' active' : '' }}"
                            data-tab-path="service-center-review-source-{{ $serviceCenter->id }}-{{ $reviewSource->id }}"
                            data-tab-group="service-center-reviews-sources-{{ $serviceCenter->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                        >
                            {{ $reviewSource->name }}
                        </button>
                    @endforeach
                </nav>

                <div class="service-center-details__subpanels">
                    @foreach ($serviceCenter->reviewSources as $reviewSource)
                        <div
                            class="service-center-details__subpanel{{ $loop->first ? ' open' : '' }}"
                            data-tab-target="service-center-review-source-{{ $serviceCenter->id }}-{{ $reviewSource->id }}"
                            data-tab-group="service-center-reviews-sources-{{ $serviceCenter->id }}"
                        ></div>
                    @endforeach
                </div>
            @endif
        </section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-photos-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        ></section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-contacts-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        ></section>
    </div>
</div>
