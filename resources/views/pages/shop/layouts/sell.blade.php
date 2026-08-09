@if ($categories->isNotEmpty())
<section class="sell">
    <div class="sell__container container-wide">
        <h2 class="sell__title">Выкуп техники</h2>
        <ul class="sell-list" data-expand-target="shop-categories">
            @foreach ($categories as $category)
                <li>
                    <x-accordion id="sell-item-{{ $category->id }}" modifier="sell">
                        <x-slot name="title">
                            <span class="sell-list__title">{{ $category->name }}</span>
                            @if (isset($prices[$category->id]) && $prices[$category->id]['max'])
                                <span class="sell-list__range">
                                    до {{ $prices[$category->id]['max'] }} руб.
                                </span>
                            @endif
                        </x-slot>
                        @include('layouts.categories-list.brands-default', [
                            'subCategories' => $category->subCategories,
                            'categoryID' => $category->id,
                            'prices' => $prices,
                            'modifier' => 'sell',
                            'attributes' => 'data-path=sell-item-' . $category->id,
                        ])
                        <div class="sell-list__breadcrumbs">
                            <button class="btn sell-list__back" data-target-breadcrumbs="sell-item-{{ $category->id }}">
                                {{ $category->name }} {{ count($category->subCategories) }}
                            </button>
                        </div>
                        @php
                            $rarr = collect([]);
                            for ($i = 1; $i <= 20; $i++) {
                                $rarr->push((object) ['name' => "Пункт_$i"]);
                            }
                        @endphp
                        @include('layouts.categories-list.brands-default', [
                            'subCategories' => $rarr,
                            'modifier' => 'point',
                            'attributes' => 'data-target=sell-item-' . $category->id,
                        ])
                    </x-accordion>
                </li>
            @endforeach
        </ul>

        <button class="btn btn--more sell__more" data-expand-path="shop-categories">Показать все</button>
    </div>
</section>
@endif
