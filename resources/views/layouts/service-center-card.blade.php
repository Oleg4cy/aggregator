<div class="service-center-card"
     data-service-center-target={{ $serviceCenter->id }}
     data-service-center-details-url="{{ route('service-centers.aside', ['id' => $serviceCenter->id]) }}">
    @if (filled(trim((string) $serviceCenter->coord)))
        <input type="hidden" name="service_center_coord" value="{{ $serviceCenter->coord }}" data-service-center-path={{ $serviceCenter->id }}>
    @endif
    <div class="service-center-card__content">
        <div class="service-center-card__header">
            <div class="service-center-card__logo">
                @if (filled(trim((string) $serviceCenter->logo)))
                    <img src="{{ $serviceCenter->logo . 'id/' . rand(1, 500) }}/100/100"
                         alt="filter img" />
                @endif
            </div>

            <div class="service-center-card__info">
                <h4 class="service-center-card__title">
                    {{ $serviceCenter->name }}
                </h4>
                <x-display-rating rating="{{ $serviceCenter->average_rating }}"/>
                <div class="service-center-card__data">
                    @if ($status = \App\Services\TitleService::timeBeforeClose($serviceCenter))
                        <p class="service-center-card__time">{!! $status !!}</p>
                    @endif
                    @if (filled(trim((string) $serviceCenter->address)))
                        <a class="service-center-card__address" href="#">
                            {{ $serviceCenter->address }}
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="service-center-card__footer">
            @if (filled(trim((string) $serviceCenter->description)))
                <p class="service-center-card__description">
                    {{ $serviceCenter->description }}
                </p>
            @endif
        </div>
    </div>
    <a
        class="service-center-card__open-new-tab"
        href="{{ route('service-centers.show', ['id' => $serviceCenter->id]) }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Открыть сервисный центр в новой вкладке"
        title="Открыть в новой вкладке"
    >
        <x-icon-new-tab aria-hidden="true" />
    </a>
</div>
