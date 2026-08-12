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
            <div class="service-center-details__overview">
                @if (filled(trim((string) $serviceCenter->description)))
                    <section class="service-center-details__overview-section">
                        <h3 class="service-center-details__section-title">
                            Описание
                        </h3>

                        <p class="service-center-details__description">
                            {{ $serviceCenter->description }}
                        </p>
                    </section>
                @endif

                @if ($serviceCenter->average_rating !== null)
                    <section class="service-center-details__overview-section">
                        <h3 class="service-center-details__section-title">
                            Общий рейтинг
                        </h3>

                        <div class="service-center-details__rating">
                            <x-display-rating rating="{{ $serviceCenter->average_rating }}" />
                        </div>
                    </section>
                @endif

                @php
                    $workingHours = $serviceCenter->workingHours->sortBy('day_of_week');
                @endphp

                @if ($workingHours->isNotEmpty())
                    <section class="service-center-details__overview-section">
                        <h3 class="service-center-details__section-title">
                            График работы
                        </h3>

                        <div class="service-center-details__working-hours">
                            @foreach ($workingHours as $day)
                                <div class="service-center-details__working-hours-row">
                                    <span class="service-center-details__working-hours-day">
                                        {{ \App\Services\DayService::getDayByNum($day->day_of_week) }}
                                    </span>

                                    <span class="service-center-details__working-hours-time{{ !$day->is_open ? ' service-center-details__working-hours-time--closed' : '' }}">
                                        @if (!$day->is_open)
                                            Выходной
                                        @else
                                            {{ $day->open_time ? \Carbon\Carbon::parse($day->open_time)->format('H:i') : '00:00' }}
                                            -
                                            {{ $day->close_time ? \Carbon\Carbon::parse($day->close_time)->format('H:i') : '23:59' }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-services-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        >
            @php
                $serviceCenterBrandsByEquipmentType = $serviceCenter->brands->groupBy('equipment_type_id');

                $serviceCapabilities = collect([
                    'online_estimate' => 'Онлайн-оценка ремонта',
                    'warranty' => 'Гарантия на ремонт',
                    'onsite_repair' => 'Выезд мастера',
                    'courier' => 'Забор и доставка курьером',
                    'original_parts' => 'Оригинальные запчасти',
                ])->filter(
                    fn ($label, $field) => (bool) $serviceCenter->{$field}
                );
            @endphp

            <div class="service-center-details__services">
                @if ($serviceCenter->equipmentTypes->isNotEmpty())
                    <section class="service-center-details__services-section">
                        <h3 class="service-center-details__section-title">
                            Ремонтируем технику
                        </h3>

                        <ul class="service-center-details__equipment-types">
                            @foreach ($serviceCenter->equipmentTypes as $equipmentType)
                                @php
                                    $brands = $serviceCenterBrandsByEquipmentType->get(
                                        $equipmentType->id,
                                        collect()
                                    );
                                @endphp

                                <li class="service-center-details__equipment-type">
                                    <x-accordion
                                        id="service-center-services-equipment-{{ $serviceCenter->id }}-{{ $equipmentType->id }}"
                                        modifier="service-center-services"
                                    >
                                        <x-slot name="title">
                                            <span class="service-center-details__equipment-type-title">
                                                {{ $equipmentType->name }}
                                            </span>
                                        </x-slot>

                                        @if ($brands->isNotEmpty())
                                            <ul class="service-center-details__brands">
                                                @foreach ($brands as $brand)
                                                    <li class="service-center-details__brand">
                                                        {{ $brand->name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </x-accordion>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($serviceCapabilities->isNotEmpty())
                    <section class="service-center-details__services-section">
                        <h3 class="service-center-details__section-title">
                            Дополнительные услуги
                        </h3>

                        <ul class="service-center-details__capabilities">
                            @foreach ($serviceCapabilities as $label)
                                <li class="service-center-details__capability">
                                    {{ $label }}
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        </section>

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
