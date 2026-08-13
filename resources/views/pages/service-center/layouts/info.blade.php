<section class="info">
    <div class="info__wrapper container">
        <div class="info__left">
            @if (filled(trim((string) $serviceCenter->address)))
            <div class="info-heading">
                <x-icon-location class="info-heading__icon" />
                <span class="info-heading__title">{{ $serviceCenter->address }}</span>
            </div>
            @endif

            <div class="info-links">
                @if (!empty($web[0]))
                    <a href="{{ $web[0] }}"
                        class="info-links__link info-links__link--site">{{ $web[0] }}</a>
                @endif
                @if (filled(trim((string) $serviceCenter->vk)))
                    <a href="vk.com/{{ $serviceCenter->vk }}"
                        class="info-links__link info-links__link--vk">vk.com/{{ $serviceCenter->vk }}</a>
                @endif
            </div>

            @if ($serviceCenter->average_rating !== null || $serviceCenter->reviewSources->isNotEmpty())
                @include('pages.service-center.layouts.info-rating', ['serviceCenterId' => $serviceCenter->id, 'averageRating' => $serviceCenter->average_rating, 'reviewSources' => $serviceCenter->reviewSources])
            @endif
            @if (filled(trim((string) $serviceCenter->description)))
            <div class="info-description">
                <h2 class="info-title mb-15">Описание</h2>
                <p class="info-description__text" id="text-slice" data-expand-target="service-center-description">
                    {{ $serviceCenter->description }}
                </p>
                {{-- <button class="btn btn--more" data-expand-path="service-center-description">
                    Показать все
                </button> --}}
            </div>
            @endif
        </div>

        <div class="info__right">
            <div class="info__right-wrapper">
                <div class="info-contacts">
                    <div class="info-heading">
                        <x-icon-contacts class="info-heading__icon" />
                        <span class="info-heading__title">Контакты</span>
                    </div>

                    <div class="info-contacts__wrapper">
                        <div class="info-contacts__phones">
                            @if (filled(trim((string) $serviceCenter->phone)))
                                <a href="tel:{{ $serviceCenter->phone }}" class="info-contacts__phone">{{ $serviceCenter->phone }}</a>
                            @endif
                            @if (is_array($additionalPhones))
                                @foreach ($additionalPhones as $phone)
                                    @if (is_scalar($phone) && filled(trim((string) $phone)))
                                        <a href="tel:{{ $phone }}" class="info-contacts__phone">{{ $phone }}</a>
                                    @endif
                                @endforeach
                            @endif
                        </div>

                        <div class="info-contacts__socials">
                            @if (filled(trim((string) $serviceCenter->whatsapp)))
                                <a href="whatsapp:{{ $serviceCenter->whatsapp }}" class="btn info-contacts__social-link">
                                    <x-icon-whatsapp-icon />
                                </a>
                            @endif
                            @if (filled(trim((string) $serviceCenter->telegram)))
                                <a href="telegram:{{ $serviceCenter->telegram }}" class="btn info-contacts__social-link">
                                    <x-icon-telegram-icon width="25" height="24" viewBox="2 -0 24 24" />
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info__hours">
                    <p class="info__countdown">
                    @if ($status = \App\Services\TitleService::timeBeforeClose($serviceCenter)) {!! $status !!} @endif
                    </p>

                    @if ($workingHours->isNotEmpty())
                        @include('pages.service-center.layouts.hours', ['workingHours' => $workingHours])
                    @endif
                </div>
            </div>

            @if ($coord)
            <div class="info-map">
                <div id='map'></div>
                <div class="info-map__overlay">
                    <button
                        href="#"
                        class="btn btn--primary btn--primary-hover-active info-map__btn info-map__btn--route"
                    >
                        <x-icon-add-icon />
                        <span>Построить маршрут</span>
                    </button>
                    {{-- <button href="#" class="btn btn--grey info-map__btn"> --}}
                    {{--     <x-icon-location-icon /> --}}
                    {{--     <span>Санкт-Петербург, ул. Ленина, д. 100</span> --}}
                    {{-- </button> --}}
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

