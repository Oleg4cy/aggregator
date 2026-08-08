@if (count($photos))
<div id="carousel-photos" class="carousel-photos modal-window__container" data-modal-target="{{ $modalTarget }}">
    <x-close-btn id="exit_fullscreen_photos" class="carousel-photos__close modal-window__close" />
    <x-carousel classMod='carousel-photos'>
        @for ($i = 0; $i < count($photos); $i++)
            <x-carousel-item classMod="carousel-photos">
                {{-- <div class="loader">
                    <img src="{{ asset('assets/images/Loading_black.gif') }}" loading="lazy" alt="loader">
                </div> --}}
                <img src="{{ $photos[$i]->name . '/id/' . $f[$i] }}/1000/700" loading="lazy" alt="фото компании {{ $shop->name }}">
            </x-carousel-item>
        @endfor
    </x-carousel>
    <div class="btn prev prev--centered carousel-photos-prev"><x-icon-slider-arrow-left /></div>
    <div class="btn next next--centered carousel-photos-next"><x-icon-slider-arrow-right /></div>
    <div class="swiper-pagination carousel-photos-pagination"></div>
</div>
@endif
