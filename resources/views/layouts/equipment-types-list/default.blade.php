@php !isset($modifier) ? $modifier = null : null; @endphp

<ul class="equipment-types-list{{ getModifiedClass('equipment-types-list', $modifier) }}">
    @foreach($equipmentTypes as $equipmentType)
    <li class="equipment-types-list__item">
        <a class="equipment-types-list__link" href="#">
            <x-icon-pc-icon class="equipment-types-list__icon" />
            {{ $equipmentType->name }}
        </a>
    </li>
    @endforeach
</ul>
