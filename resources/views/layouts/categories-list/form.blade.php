@php !isset($modifier) ? $modifier = null : null; @endphp

<ul
    class="categories-list{{ getModifiedClass('categories-list', [$modifier, 'form']) }}">
    @foreach ($equipmentTypes as $equipmentType)
        <li
            class="categories-list__item">
            <x-checkbox-square label="{{ $equipmentType->name }}" labelPos="back" value="{{ $equipmentType->id }}" name="filter-equipment-type" autocomplete="on">
                <x-icon-pc-icon fill="transparent" />
            </x-checkbox-square>
            <button
                class="btn categories-list__into"
                data-brand-path="{{ $equipmentType->id }}">
                <x-icon-chevron-down />
            </button>
            <div class="categories-list__brands"
                data-brand-target="{{ $equipmentType->id }}">
                <div class="categories-list__breadcrumbs">
                    <button class="btn categories-list__back"
                        data-brand-close="{{ $equipmentType->id }}">
                        {{ $equipmentType->name }} ({{ count($equipmentType->brands) }})
                    </button>
                </div>
                @include('layouts.categories-list.brands-form', [
                    'brands' => $equipmentType->brands,
                    'modifier' => $modifier,
                    'equipmentTypeId' => $equipmentType->id,
                ])
            </div>
        </li>
    @endforeach
</ul>
