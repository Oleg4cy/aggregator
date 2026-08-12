<div class="service-center-details">
    <button
        type="button"
        class="service-center-details__back"
        data-service-center-details-back
    >
        <x-icon-arrow-left />
        <span>Назад к списку</span>
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

    @if (filled(trim((string) $serviceCenter->description)))
        <p class="service-center-details__description">
            {{ $serviceCenter->description }}
        </p>
    @endif
</div>
