@if (count($photos))
<section class="carousel-preview">
    <x-carousel classMod='carousel-preview'>
        @for ($i = 0; $i < count($photos); $i++)
            <x-carousel-item classMod="carousel-preview"
                addAttributes="data-modal-path={{ $modalPath }} data-modal-event=photosCarousel data-carousel-preview={{ $i }}">
                <img class="carousel-preview__img" src="{{ $photos[$i]->name . '/id/' . $f[$i] }}/240/240"
                    alt="фото сервисного центра {{ $serviceCenter->name }}" />
            </x-carousel-item>
        @endfor
    </x-carousel>
    <div class="btn prev prev--centered carousel-preview-prev"><x-icon-slider-arrow-left /></div>
    <div class="btn next next--centered carousel-preview-next"><x-icon-slider-arrow-right /></div>
</section>
@endif
