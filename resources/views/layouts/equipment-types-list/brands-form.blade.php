@php !isset($modifier) ? $modifier = null : null; @endphp

<ul class="equipment-types-list{{ getModifiedClass('equipment-types-list', [$modifier, 'brand']) }}">
    @foreach ($brands as $brand)
    <li class="equipment-types-list__item">
        <x-checkbox-square label="{{ $brand->name }}" labelPos="back" value="{{ $brand->id }}" inputAttributes="data-filter-equipment-type={{ $equipmentTypeId }}" autocomplete="on">
            {{-- <x-icon-pc-icon fill="transparent"/> --}}
        </x-checkbox-square>
        {{-- <div class="equipment-types-list__icon"></div> --}}
        {{-- <div class="equipment-types-list__info"> --}}
        {{--     <p class="equipment-types-list__brand">{{ $item->name }}</p> --}}
        {{--     <!-- <p class="equipment-types-list__price">от 500 руб.</p> --> --}}
        {{-- </div> --}}
    </li>
    @endforeach
</ul>

