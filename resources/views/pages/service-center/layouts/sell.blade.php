@php
    $buybackEquipmentTypes = $equipmentTypes->filter(function ($equipmentType) use ($buybackPrices) {
        return isset($buybackPrices[$equipmentType->id])
            && $equipmentType->brands->contains(function ($brand) use ($buybackPrices, $equipmentType) {
                return $buybackPrices[$equipmentType->id]['items']->has($brand->id);
            });
    });
@endphp
@if ($buybackEquipmentTypes->isNotEmpty())
<section class="sell">
    <div class="sell__container container-wide">
        <h2 class="sell__title">Выкуп техники</h2>
        <ul class="sell-list" data-expand-target="service-center-equipment-types">
            @foreach ($buybackEquipmentTypes as $equipmentType)
                <li>
                    @php
                        $buybackBrands = $equipmentType->brands->filter(function ($brand) use ($buybackPrices, $equipmentType) {
                            return $buybackPrices[$equipmentType->id]['items']->has($brand->id);
                        });
                    @endphp
                    <x-accordion id="sell-item-{{ $equipmentType->id }}" modifier="sell">
                        <x-slot name="title">
                            <span class="sell-list__title">{{ $equipmentType->name }}</span>
                            @if (isset($buybackPrices[$equipmentType->id]) && $buybackPrices[$equipmentType->id]['max'])
                                <span class="sell-list__range">
                                    до {{ $buybackPrices[$equipmentType->id]['max'] }} руб.
                                </span>
                            @endif
                        </x-slot>
                        @include('layouts.equipment-types-list.brands-default', [
                            'brands' => $buybackBrands,
                            'equipmentTypeId' => $equipmentType->id,
                            'buybackPrices' => $buybackPrices,
                            'modifier' => 'sell',
                            'attributes' => 'data-path=sell-item-' . $equipmentType->id,
                        ])
                        <div class="sell-list__breadcrumbs">
                            <button class="btn sell-list__back" data-target-breadcrumbs="sell-item-{{ $equipmentType->id }}">
                                {{ $equipmentType->name }} {{ count($buybackBrands) }}
                            </button>
                        </div>
                        @php
                            $rarr = collect([]);
                            for ($i = 1; $i <= 20; $i++) {
                                $rarr->push((object) ['name' => "Пункт_$i"]);
                            }
                        @endphp
                        @include('layouts.equipment-types-list.brands-default', [
                            'brands' => $rarr,
                            'modifier' => 'point',
                            'attributes' => 'data-target=sell-item-' . $equipmentType->id,
                        ])
                    </x-accordion>
                </li>
            @endforeach
        </ul>

        <button class="btn btn--more sell__more" data-expand-path="service-center-equipment-types">Показать все</button>
    </div>
</section>
@endif
