@php !isset($modifier) ? $modifier = null : null; @endphp

<ul class="categories-list{{ getModifiedClass('categories-list', [$modifier, 'grid']) }}" {{ $attributes ?? '' }}>
    @foreach ($brands as $brand)
        <li class="categories-list__item"
            {{ isset($itemAttributes) ? $itemAttributes($brand->id) : '' }}>
            <div class="categories-list__icon"></div>
            <div class="categories-list__info">
                <p class="categories-list__brand">
                    {{ $brand->name }}</p>
                @if (isset($buybackPrices) && isset($equipmentTypeId) && isset($buybackPrices[$equipmentTypeId]['items'][$brand->id]))
                    <p class="categories-list__price">от
                        {{ $buybackPrices[$equipmentTypeId]['items'][$brand->id]->price }} руб.
                    </p>
                @endif
            </div>
        </li>
    @endforeach
</ul>
