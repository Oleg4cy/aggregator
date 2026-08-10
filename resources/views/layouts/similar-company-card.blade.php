<div class="similar-company-card">
    <div class="similar-company-card__img-box">
        <img class="similar-company-card__img" src="{{ asset('assets/img/item/card-photo.jpg') }}" alt="Фото сервисного центра" />
    </div>
    <div class="similar-company-card__info-box">
        <h3 class="similar-company-card__title"><a href="/service-centers/{{ $similar->id }}">{{ $similar->name }}</a></h3>
        <x-display-rating rating="{{ $similar->average_rating }}" disabled={{true}} serviceCenterId="{{ $similar->id }}" classMod="similar-company"/>
        @if ($status = \App\Services\TitleService::timeBeforeClose($similar, true))
            <p class="similar-company-card__status">{!! $status !!}</p>
        @endif
        @if (filled(trim((string) $similar->address)))
            <p class="similar-company-card__address">{{ $similar->address }}</p>
        @endif
    </div>
</div>
