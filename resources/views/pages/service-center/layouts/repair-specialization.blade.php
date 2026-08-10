@if ($equipmentTypes->isNotEmpty())
    <section class="repair-specialization">
        <div class="repair-specialization__container container-wide">
            <h2 class="repair-specialization__title">Ремонтируем технику</h2>

            <ul class="repair-specialization-list">
                @foreach ($equipmentTypes as $equipmentType)
                    <li>
                        <x-accordion
                            id="repair-specialization-item-{{ $equipmentType->id }}"
                            modifier="repair-specialization"
                        >
                            <x-slot name="title">
                                <span class="repair-specialization-list__title">
                                    {{ $equipmentType->name }}
                                </span>
                            </x-slot>

                            @if ($equipmentType->brands->isNotEmpty())
                                @include('layouts.equipment-types-list.brands-default', [
                                    'brands' => $equipmentType->brands,
                                    'modifier' => 'repair-specialization',
                                ])
                            @endif
                        </x-accordion>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
