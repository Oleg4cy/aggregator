<div class="service-center-card" data-service-center-target={{ $serviceCenter->id }}>
    @if (filled(trim((string) $serviceCenter->coord)))
        <input type="hidden" name="service_center_coord" value="{{ $serviceCenter->coord }}" data-service-center-path={{ $serviceCenter->id }}>
    @endif
    <div class="service-center-card__content">
        <div class="service-center-card__header">
            <div class="service-center-card__info">
                <h4 class="service-center-card__title">
                    {{ $serviceCenter->name }}
                </h4>
                <x-display-rating rating="{{ $serviceCenter->average_rating }}"/>
                <div class="service-center-card__data">
                    @if ($status = \App\Services\TitleService::timeBeforeClose($serviceCenter))
                        <p class="service-center-card__time">{!! $status !!}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
