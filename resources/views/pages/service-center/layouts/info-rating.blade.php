@if ($averageRating !== null || $reviewSources->isNotEmpty())
<div class="info-rating">
    <h2 class="info-title mb-12">Общий рейтинг</h2>
    <x-display-rating rating="{{ $averageRating }}" disabled={{ true }}
        serviceCenterId="{{ $serviceCenterId }}" classMod="info-average" />
    <table class="info-rating__table">
        @foreach ($reviewSources as $reviewSource)
            <tr>
                <th class="info-rating__logo">
                    @switch($reviewSource->name)
                        @case('Яндекс карты')
                            <x-icon-yandex-logo
                                class="info-rating__source-logo"
                                role="img"
                                aria-label="Яндекс карты"
                            />
                            @break

                        @case('Google maps')
                            <x-icon-google-logo
                                class="info-rating__source-logo"
                                width="90"
                                height="31"
                                viewBox="5 13 110 38"
                                role="img"
                                aria-label="Google"
                            />
                            @break

                        @case('2Gis')
                            <x-icon-2gis-logo
                                class="info-rating__source-logo"
                                width="87"
                                height="24"
                                role="img"
                                aria-label="2GIS"
                            />
                            @break

                        @case('Авито')
                            <x-icon-avito-logo
                                class="info-rating__source-logo"
                                role="img"
                                aria-label="Авито"
                            />
                            @break
                    @endswitch
                </th>
                <td>
                    <x-display-rating rating="{{ $reviewSource->pivot->rating }}" disabled={{ true }}
                        classMod="info-review-sources" />
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif
