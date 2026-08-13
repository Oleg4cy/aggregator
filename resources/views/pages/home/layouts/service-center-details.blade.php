<div class="service-center-details">
    <button
        type="button"
        class="service-center-details__close"
        data-service-center-details-close
        aria-label="Закрыть"
        title="Закрыть"
    >
        <x-icon-close aria-hidden="true" />
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

                @if ($serviceCenter->average_rating !== null || $serviceCenter->reviewSources->isNotEmpty())
                    <section class="service-center-details__overview-section">
                        <h3 class="service-center-details__section-title">
                            Общий рейтинг
                        </h3>

                        <div class="service-center-details__rating">
                            @if ($serviceCenter->average_rating !== null)
                                <x-display-rating
                                    rating="{{ $serviceCenter->average_rating }}"
                                    disabled={{ true }}
                                    classMod="service-center-overview-average"
                                />
                            @endif

                            @if ($serviceCenter->reviewSources->isNotEmpty())
                                <div class="service-center-details__rating-sources">
                                    @foreach ($serviceCenter->reviewSources as $reviewSource)
                                        <div class="service-center-details__rating-source">
                                            <div class="service-center-details__rating-source-logo">
                                                @switch($reviewSource->name)
                                                    @case('Яндекс карты')
                                                        <x-icon-yandex-logo
                                                            class="service-center-details__review-source-logo"
                                                            role="img"
                                                            aria-label="Яндекс карты"
                                                        />
                                                        @break

                                                    @case('Google maps')
                                                        <x-icon-google-logo
                                                            class="service-center-details__review-source-logo"
                                                            width="90"
                                                            height="31"
                                                            viewBox="5 13 110 38"
                                                            role="img"
                                                            aria-label="Google"
                                                        />
                                                        @break

                                                    @case('2Gis')
                                                        <x-icon-2gis-logo
                                                            class="service-center-details__review-source-logo"
                                                            width="87"
                                                            height="24"
                                                            role="img"
                                                            aria-label="2GIS"
                                                        />
                                                        @break

                                                    @case('Авито')
                                                        <x-icon-avito-logo
                                                            class="service-center-details__review-source-logo"
                                                            role="img"
                                                            aria-label="Авито"
                                                        />
                                                        @break
                                                @endswitch
                                            </div>

                                            <x-display-rating
                                                rating="{{ $reviewSource->pivot->rating }}"
                                                disabled={{ true }}
                                                classMod="service-center-overview-source"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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
                                <div @class([
                                    'service-center-details__working-hours-row',
                                    'service-center-details__working-hours-row--open' => $day->is_open,
                                    'service-center-details__working-hours-row--closed' => !$day->is_open,
                                    'service-center-details__working-hours-row--weekend' => $day->day_of_week > 5,
                                ])>
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
                        >
                            <div class="service-center-details__review-source">
                                <div class="service-center-details__review-source-summary">
                                    <div class="service-center-details__review-source-rating">
                                        <x-display-rating
                                            rating="{{ $reviewSource->pivot->rating }}"
                                            disabled={{ true }}
                                            classMod="service-center-review-source"
                                        />
                                    </div>

                                    <span class="service-center-details__review-count">
                                        {{ $reviewSource->pivot->rating_count }}
                                        {{ getNumEnding(
                                            (int) $reviewSource->pivot->rating_count,
                                            ['оценка', 'оценки', 'оценок']
                                        ) }}
                                    </span>

                                    @if (filled(trim((string) $reviewSource->pivot->link)))
                                        <a
                                            href="{{ $reviewSource->pivot->link }}"
                                            class="service-center-details__review-source-link"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            Перейти к отзывам
                                        </a>
                                    @endif
                                </div>

                                @php
                                    $comments = json_decode($reviewSource->pivot->comments);
                                    $comments = is_array($comments)
                                        ? array_filter($comments, 'is_object')
                                        : [];
                                @endphp

                                @if ($comments)
                                    <div class="service-center-details__review-comments">
                                        @foreach ($comments as $comment)
                                            @include('layouts.comment', ['comment' => $comment])
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-photos-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        >
            <div class="service-center-details__photos">
                @if (count($photos) > 0)
                    <div class="service-center-details__photo-grid">
                        @foreach ($photos as $photo)
                            <div class="service-center-details__photo">
                                <img
                                    src="{{ $photo->name . '/id/' . rand(10, 100) . '/400/300' }}"
                                    alt="Фото сервисного центра {{ $serviceCenter->name }}"
                                    loading="lazy"
                                />
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="service-center-details__empty">
                        Фотографии пока не добавлены
                    </p>
                @endif
            </div>
        </section>

        <section
            class="service-center-details__panel"
            data-tab-target="service-center-contacts-{{ $serviceCenter->id }}"
            data-tab-group="service-center-main-{{ $serviceCenter->id }}"
        >
            @php
                $hasContacts =
                    filled(trim((string) $serviceCenter->address))
                    || filled(trim((string) $serviceCenter->phone))
                    || count($additionalPhones) > 0
                    || filled(trim((string) $serviceCenter->whatsapp))
                    || filled(trim((string) $serviceCenter->telegram))
                    || count($emails) > 0
                    || count($web) > 0
                    || filled(trim((string) $serviceCenter->vk));
            @endphp

            <div class="service-center-details__contacts">
                @if (!$hasContacts)
                    <p class="service-center-details__empty">
                        Контактные данные не указаны
                    </p>
                @else
                    @if (filled(trim((string) $serviceCenter->address)))
                        <section class="service-center-details__contact-section">
                            <h3 class="service-center-details__section-title">
                                Адрес
                            </h3>

                            <p class="service-center-details__contact-address">
                                {{ $serviceCenter->address }}
                            </p>
                        </section>
                    @endif

                    @if (filled(trim((string) $serviceCenter->phone)) || count($additionalPhones) > 0)
                        <section class="service-center-details__contact-section">
                            <h3 class="service-center-details__section-title">
                                Телефоны
                            </h3>

                            <div class="service-center-details__contact-list">
                                @if (filled(trim((string) $serviceCenter->phone)))
                                    <a
                                        href="tel:{{ $serviceCenter->phone }}"
                                        class="service-center-details__contact-link"
                                    >
                                        {{ $serviceCenter->phone }}
                                    </a>
                                @endif

                                @foreach ($additionalPhones as $phone)
                                    <a
                                        href="tel:{{ $phone }}"
                                        class="service-center-details__contact-link"
                                    >
                                        {{ $phone }}
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if (filled(trim((string) $serviceCenter->whatsapp)) || filled(trim((string) $serviceCenter->telegram)))
                        <section class="service-center-details__contact-section">
                            <h3 class="service-center-details__section-title">
                                Мессенджеры
                            </h3>

                            <div class="service-center-details__messengers">
                                @if (filled(trim((string) $serviceCenter->whatsapp)))
                                    <a
                                        href="whatsapp:{{ $serviceCenter->whatsapp }}"
                                        class="service-center-details__messenger"
                                    >
                                        <x-icon-whatsapp-icon aria-hidden="true" />
                                        <span>WhatsApp</span>
                                    </a>
                                @endif

                                @if (filled(trim((string) $serviceCenter->telegram)))
                                    <a
                                        href="telegram:{{ $serviceCenter->telegram }}"
                                        class="service-center-details__messenger"
                                    >
                                        <x-icon-telegram-icon
                                            width="25"
                                            height="24"
                                            viewBox="2 -0 24 24"
                                            aria-hidden="true"
                                        />
                                        <span>Telegram</span>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif

                    @if (count($emails) > 0)
                        <section class="service-center-details__contact-section">
                            <h3 class="service-center-details__section-title">
                                Email
                            </h3>

                            <div class="service-center-details__contact-list">
                                @foreach ($emails as $email)
                                    <a
                                        href="mailto:{{ $email }}"
                                        class="service-center-details__contact-link"
                                    >
                                        {{ $email }}
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if (count($web) > 0 || filled(trim((string) $serviceCenter->vk)))
                        <section class="service-center-details__contact-section">
                            <h3 class="service-center-details__section-title">
                                Сайт и соцсети
                            </h3>

                            <div class="service-center-details__contact-list">
                                @foreach ($web as $site)
                                    @php
                                        $siteValue = trim((string) $site);
                                        $siteUrl = str_starts_with($siteValue, 'http://')
                                            || str_starts_with($siteValue, 'https://')
                                                ? $siteValue
                                                : 'https://' . $siteValue;
                                    @endphp

                                    <a
                                        href="{{ $siteUrl }}"
                                        class="service-center-details__contact-link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ $siteValue }}
                                    </a>
                                @endforeach

                                @if (filled(trim((string) $serviceCenter->vk)))
                                    @php
                                        $vkValue = trim((string) $serviceCenter->vk);
                                        $vkUrl = str_starts_with($vkValue, 'http://')
                                            || str_starts_with($vkValue, 'https://')
                                                ? $vkValue
                                                : 'https://vk.com/' . ltrim(
                                                    preg_replace('#^vk\.com/#', '', $vkValue),
                                                    '/'
                                                );
                                    @endphp

                                    <a
                                        href="{{ $vkUrl }}"
                                        class="service-center-details__contact-link"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ $vkValue }}
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif
                @endif
            </div>
        </section>
    </div>
</div>
