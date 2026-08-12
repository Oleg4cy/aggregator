<section class="filter">
    <div class="filter__container">
        <aside class="filter__aside">
            <div class="filter__results" data-service-center-results>
                @include('layouts.service-center-list', ['serviceCenters' => $serviceCenters])
            </div>
            <div class="filter__details" data-service-center-details hidden></div>
        </aside>
        <div id="filter-map" class="filter__map"></div>
    </div>
</section>
