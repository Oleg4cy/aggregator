@if ($averageRating !== null || $reviewSources->isNotEmpty())
<div class="info-rating">
    <h2 class="info-title mb-12">Общий рейтинг</h2>
    <x-display-rating rating="{{ $averageRating }}" disabled={{ true }}
        serviceCenterId="{{ $serviceCenterId }}" classMod="info-average" />
    <table class="info-rating__table">
        @foreach ($reviewSources as $reviewSource)
            <tr>
                <th class="info-rating__logo">
                    <img src="{{ asset("resources-assets/svg/$reviewSource->logo") }}"
                        alt="{{ $reviewSource->name }}" />
                </th>
                <td>
                    <x-display-rating rating="{{ $reviewSource->pivot->rating }}" disabled={{ true }}
                        classMod="info-services" />
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif
