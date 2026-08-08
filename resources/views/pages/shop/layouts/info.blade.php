<section class="info">
    <div class="info__wrapper container">
        <div class="info__left">
            @if (filled(trim((string) $shop->address)))
            <div class="info-heading">
                <x-icon-location />
                <span class="info-heading__title">{{ $shop->address }}</span>
            </div>
            @endif

            <div class="info-links">
                @if (!empty($web[0]))
                    <a href="{{ $web[0] }}"
                        class="info-links__link info-links__link--site">{{ $web[0] }}</a>
                @endif
                @if (filled(trim((string) $shop->vk)))
                    <a href="vk.com/{{ $shop->vk }}"
                        class="info-links__link info-links__link--vk">vk.com/{{ $shop->vk }}</a>
                @endif
            </div>

            @if ($shop->average_rating !== null || $shop->services->isNotEmpty())
                @include('pages.shop.layouts.info-rating', ['shopID' => $shop->id, 'averageRating' => $shop->average_rating, 'services' => $shop->services])
            @endif
            @if (filled(trim((string) $shop->description)))
            <div class="info-description">
                <h2 class="info-title mb-15">Описание</h2>
                <p class="info-description__text" id="text-slice" data-expand-target="shop-description">
                    {{ $shop->description }}
                </p>
                {{-- <button class="btn btn--more" data-expand-path="shop-description">
                    Показать все
                </button> --}}
            </div>
            @endif
        </div>

        <div class="info__right">
            <div class="info__right-wrapper">
                <div class="info-contacts">
                    <div class="info-heading">
                        <x-icon-contacts />
                        <span class="info-heading__title">Контакты</span>
                    </div>

                    <div class="info-contacts__wrapper">
                        <div class="info-contacts__phones">
                            @if (filled(trim((string) $shop->phone)))
                                <a href="tel:{{ $shop->phone }}" class="info-contacts__phone">{{ $shop->phone }}</a>
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
                            @if (filled(trim((string) $shop->whatsapp)))
                                <a href="whatsapp:{{ $shop->whatsapp }}" class="btn info-contacts__social-link">
                                    <x-icon-whatsapp-icon />
                                </a>
                            @endif
                            @if (filled(trim((string) $shop->telegram)))
                                <a href="telegram:{{ $shop->telegram }}" class="btn info-contacts__social-link">
                                    <x-icon-telegram-icon width="25" height="24" viewBox="2 -0 24 24" />
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info__hours">
                    <p class="info__countdown">
                    @if ($status = \App\Services\TitleService::timeBeforeClose($shop)) {!! $status !!} @endif
                    </p>

                    @if ($workingMode->isNotEmpty())
                        @include('pages.shop.layouts.hours', ['workingMode' => $workingMode])
                    @endif
                </div>
            </div>

            @if ($coord)
            <div class="info-map">
                <div id='map'></div>
                <div class="info-map__overlay">
                    <button href="#" class="btn btn--primary info-map__btn">
                        <x-icon-add-icon />
                        <span>Построить маршрут</span>
                    </button>
                    <button href="#" class="btn btn--grey info-map__btn">
                        <x-icon-location-icon />
                        <span>Санкт-Петербург, ул. Ленина, д. 100</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
