<section class="filter">
    <div class="filter__container">
        @include('layouts.aside')
        <div class="flex-ctr correct filter__wrapper" data-correct>
            @include('layouts.service-center-list', ['serviceCenters' => $serviceCenters])
            <div id="filter-map" class="filter__map"></div>
        </div>
    </div>
    <p class="filter__text" data-expand-target="filter-text">Найдите сервисный центр по типу техники, бренду, расположению и дополнительным услугам. Сравните график работы, отзывы и доступные возможности сервисных центров, чтобы выбрать подходящий вариант.</p>
    <button class="btn filter__text-btn" data-expand-path="filter-text">Показать все</button>
</section>

