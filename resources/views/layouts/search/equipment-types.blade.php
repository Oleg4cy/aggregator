<section class="search{{ getModifiedClass('search', [$modifier, 'equipment-types']) }}">
    <div class="search__form">
        <form onsubmit="event.preventDefault();" role="search">
            <input id="{{ $inputID }}"
                class="search__input"
                name="{{ $inputName }}" type="search" placeholder="Поиск" autofocus required />
            <button class="search__button" type="submit">
                <x-icon-search-icon />
            </button>
        </form>
    </div>
    @include('layouts.equipment-types-list.' . $equipmentTypeListType , ['equipmentTypes' => $equipmentTypes, 'modifier' => $modifier])
    <div class="search__action">
        <button class="btn btn--primary search__btn search__btn--selection">
            <span class="search__icon search__icon--apply"></span>
            <span class="search__apply-title">Не выбрано</span>
            <span class="search__apply-count"></span>
        </button>
        <button class="btn search__btn search__btn--clear disabled">
            {{-- <x-icon-trash /> --}}
            <span class="search__icon search__icon--clear"></span>
            <span class="search__clear-title">Сбросить всё</span>
            <span class="search__clear-count"></span>
        </button>
    </div>
</section>
