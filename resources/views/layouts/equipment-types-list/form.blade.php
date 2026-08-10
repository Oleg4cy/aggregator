@php !isset($modifier) ? $modifier = null : null; @endphp

<ul
    class="equipment-types-list{{ getModifiedClass('equipment-types-list', [$modifier, 'form']) }}">
    @foreach ($equipmentTypes as $equipmentType)
        <li
            class="equipment-types-list__item">
            <x-checkbox-square label="{{ $equipmentType->name }}" labelPos="back" value="{{ $equipmentType->id }}" name="filter-equipment-type" autocomplete="on">
                <x-icon-pc-icon fill="transparent" />
            </x-checkbox-square>
            <button
                class="btn equipment-types-list__into"
                data-brand-path="{{ $equipmentType->id }}">
                <x-icon-chevron-down />
            </button>
            <div class="equipment-types-list__brands"
                data-brand-target="{{ $equipmentType->id }}">
                <div class="equipment-types-list__breadcrumbs">
                    <button class="btn equipment-types-list__back"
                        data-brand-close="{{ $equipmentType->id }}">
                        {{ $equipmentType->name }} ({{ count($equipmentType->brands) }})
                    </button>
                </div>
                @include('layouts.equipment-types-list.brands-form', [
                    'brands' => $equipmentType->brands,
                    'modifier' => $modifier,
                    'equipmentTypeId' => $equipmentType->id,
                ])
            </div>
        </li>
    @endforeach
</ul>
