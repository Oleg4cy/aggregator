<section class="feedback">
    <div class="container feedback__content">
        <div class="feedback__left">
            <div class="feedback__heading">
                <h2 class="feedback__title">Отзывы</h2>
                <x-display-rating rating="{{ $shop->average_rating }}" disabled={{ true }}
                    classMod="feedback-average" />
            </div>

            <p class="feedback__description">
                Все актуальные отзывы о сервисном центре можно посмотреть
                на странице компании на соответствующем сервисе
            </p>

            <div class="feedback__tabset">
                @foreach ($shop->services as $service)
                    <input class="feedback__checkbox" type="radio" name="tabset" id="tab-{{ $service->id }}"
                        {{ $service->id === 1 ? 'checked' : '' }} autocomplete="off" />
                    <label class="feedback__label" for="tab-{{ $service->id }}"
                        data-tab-path="tab-feedback-{{ $service->id }}" data-tab-group="tab-feedback">
                        <img src="{{ asset("resources-assets/svg/$service->logo") }}" alt="{{ $service->name }}" />
                        <span class="btn feedback__number">{{ $service->pivot->rating_count }}
                            {{ getNumEnding((int) $service->rating_count, ['оценка', 'оценки', 'оценок']) }}</span>
                        <x-display-rating rating="{{ $service->pivot->rating }}" disabled={{ true }}
                            classMod="feedback-service" />
                    </label>
                @endforeach
            </div>
        </div>
        <div class="feedback__panels">
            @foreach ($shop->services as $service)
                <div class="feedback__tab{{ $service->id == 1 ? ' open' : '' }}"
                    data-tab-target="tab-feedback-{{ $service->id }}" data-tab-group="tab-feedback">
                    @include('layouts.comments-list', [
                        'service' => $service,
                        'filterID' => "comments_filter_$service->id",
                    ])
                </div>
            @endforeach
        </div>
        <div class="feedback__list">
            @foreach ($shop->services as $service)
                <x-accordion id="feedback-item-{{ $service->id }}" modifier="feedback">
                    <x-slot name="title">
                        <img class="feedback__logo" src="{{ asset("resources-assets/svg/$service->logo") }}"
                            alt="{{ $service->name }}" />
                        <div class="feedback__info">
                            <x-display-rating rating="{{ $service->pivot->rating }}" disabled={{ true }}
                                classMod="feedback-service" />
                            <span class="btn feedback__number">{{ $service->pivot->rating_count }}
                                {{ getNumEnding((int) $service->rating_count, ['оценка', 'оценки', 'оценок']) }}</span>
                        </div>
                    </x-slot>
                    @include('layouts.comments-list', [
                        'service' => $service,
                        'filterID' => "mobile_comments_filter_$service->id",
                    ])
                </x-accordion>
            @endforeach
        </div>
    </div>
</section>
