@php !isset($modifier) ? $modifier = null : null; @endphp

<ul class="categories-list{{ getModifiedClass('categories-list', $modifier) }}">
    @foreach($equipmentTypes as $equipmentType)
    <li class="categories-list__item">
        <a class="categories-list__link" href="#">
            <x-icon-pc-icon class="categories-list__icon" />
            {{ $equipmentType->name }}
        </a>
    </li>
    @endforeach
</ul>
