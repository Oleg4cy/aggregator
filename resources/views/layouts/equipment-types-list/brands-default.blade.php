@php !isset($modifier) ? $modifier = null : null; @endphp

<ul class="equipment-types-list{{ getModifiedClass('equipment-types-list', [$modifier, 'grid']) }}" {{ $attributes ?? '' }}>
    @foreach ($brands as $brand)
        <li class="equipment-types-list__item"
            {{ isset($itemAttributes) ? $itemAttributes($brand->id) : '' }}>
            <div class="equipment-types-list__icon"></div>
            <div class="equipment-types-list__info">
                <p class="equipment-types-list__brand">
                    {{ $brand->name }}</p>
                @if (isset($buybackPrices) && isset($equipmentTypeId) && isset($buybackPrices[$equipmentTypeId]['items'][$brand->id]))
                    <p class="equipment-types-list__price">от
                        {{ $buybackPrices[$equipmentTypeId]['items'][$brand->id]->price }} руб.
                    </p>
                @endif
            </div>
        </li>
    @endforeach
</ul>
