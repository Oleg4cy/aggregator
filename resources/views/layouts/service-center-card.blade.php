<div class="service-center-card" data-service-center-target={{ $serviceCenter->id }}>
    @if (filled(trim((string) $serviceCenter->coord)))
        <input type="hidden" name="service_center_coord" value="{{ $serviceCenter->coord }}" data-service-center-path={{ $serviceCenter->id }}>
    @endif
    <div class="service-center-card__content">
        <div class="service-center-card__header">
            <div class="service-center-card__logo">
                @if (filled(trim((string) $serviceCenter->logo)))
                    <img src="{{ $serviceCenter->logo . 'id/' . rand(1, 500) }}/100/100" alt="filter img" />
                @endif
            </div>
            <div class="service-center-card__info">
                <h4 class="service-center-card__title">
                    <a href="{{ route('service-centers.show', ['id' => $serviceCenter->id]) }}">{{ $serviceCenter->name }}</a>
                </h4>
                <x-display-rating rating="{{ $serviceCenter->average_rating }}"/>
                <div class="service-center-card__data">
                    @if ($status = \App\Services\TitleService::timeBeforeClose($serviceCenter))
                        <p class="service-center-card__time">{!! $status !!}</p>
                    @endif
                    @if (filled(trim((string) $serviceCenter->address)))
                        <a class="service-center-card__address" href="#">{{ $serviceCenter->address }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="service-center-card__footer">
            @if (filled(trim((string) $serviceCenter->description)))
                <p class="service-center-card__description">{{ $serviceCenter->description }}</p>
            @endif
            <a class="btn btn--secondary service-center-card__action" href="#">Отправить заявку</a>
        </div>
    </div>
    <div class="service-center-card__contacts">
        <a class="btn service-center-card__contact service-center-card__contact--mobile-btn" href="#">
            Заявка
        </a>
        @if (filled(trim((string) $serviceCenter->telegram)))
        <x-social-item className="service-center-card__contact service-center-card__contact--telegram">
            <x-icon-telegram-icon />
        </x-social-item>
        @endif
        @if (filled(trim((string) $serviceCenter->whatsapp)))
        <x-social-item className="service-center-card__contact service-center-card__contact--whatsapp">
            <x-icon-whatsapp-icon />
        </x-social-item>
        @endif
        @if (filled(trim((string) $serviceCenter->phone)))
        <x-social-item className="service-center-card__contact service-center-card__contact--tel">
            <x-icon-tel-icon />
        </x-social-item>
        @endif
        <button class="btn service-center-card__show-location" data-service-center-view="{{ $serviceCenter->id }}"><x-icon-map-location /></button>
    </div>
</div>
