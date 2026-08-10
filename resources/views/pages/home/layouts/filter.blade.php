<section class="filter">
    <div class="filter__container">
        @include('layouts.aside')
        <div class="flex-ctr correct filter__wrapper" data-correct>
            @include('layouts.service-center-list', ['serviceCenters' => $serviceCenters])
            <div id="filter-map" class="filter__map"></div>
        </div>
    </div>
    <p class="filter__text" data-expand-target="filter-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
    <button class="btn filter__text-btn" data-expand-path="filter-text">Показать все</button>
</section>

