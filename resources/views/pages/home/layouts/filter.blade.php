<section class="filter">
    <div class="filter__container">
        <aside class="filter__aside">
            @include('layouts.service-center-list', ['serviceCenters' => $serviceCenters])
        </aside>
        <div id="filter-map" class="filter__map"></div>
    </div>
</section>
