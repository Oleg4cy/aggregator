<div class="similar-location">
    <div class="similar-location__list" data-expand-target="similar-location">
        @foreach (\App\Models\Area::getByCityID($cityID)->get() as $area)
        <a href="#">{{ $area->name }}</a>
        @endforeach
    </div>
    {{-- <button class="btn btn--more similar-location__more" data-button="district" data-expand-path="similar-equipment-types"> --}}
    <button class="btn btn--more similar-location__more" data-expand-path="similar-location" data-expand-width='fixed'>
        Показать все
    </button>
</div>


