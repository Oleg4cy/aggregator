<section class="similar-companies container">
    <h2 class="similar-companies__title">Похожие сервисные центры</h2>

    <div class="similar-companies__carousel">
        <x-carousel classMod='similar-companies' alt="фото сервисного центра" >
            @foreach ($similars as $similar)
                <x-carousel-item classMod="similar-companies" >
                    @include('layouts.similar-company-card', ['similar' => $similar])
                </x-carousel-item>
            @endforeach
        </x-carousel>
        <div class="similar-companies__navigation">
            <div class="btn prev prev--centered similar-companies-prev"><x-icon-slider-arrow-left /></div>
            <div class="btn next next--centered similar-companies-next"><x-icon-slider-arrow-right /></div>
        </div>
    </div>
</section>

