@if ($averageRating !== null || $services->isNotEmpty())
<div class="info-rating">
    <h2 class="info-title mb-12">Общий рейтинг</h2>
    <x-display-rating rating="{{ $averageRating }}" disabled={{ true }}
        shopID="{{ $shopID }}" classMod="info-average" />
    <table class="info-rating__table">
        @foreach ($services as $service)
            <tr>
                <th class="info-rating__logo">
                    <img src="{{ asset("resources-assets/svg/$service->logo") }}"
                        alt="{{ $service->name }}" />
                </th>
                <td>
                    <x-display-rating rating="{{ $service->pivot->rating }}" disabled={{ true }}
                        classMod="info-services" />
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endif
