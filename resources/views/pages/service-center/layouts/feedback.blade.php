<section class="feedback">
    <div class="container feedback__content">
        <div class="feedback__left">
            <div class="feedback__heading">
                <h2 class="feedback__title">Отзывы</h2>
                <x-display-rating rating="{{ $serviceCenter->average_rating }}" disabled={{ true }}
                    classMod="feedback-average" />
            </div>

            <p class="feedback__description">
                Все актуальные отзывы о сервисном центре можно посмотреть
                на странице компании на соответствующем сервисе
            </p>

            <div class="feedback__tabset">
                @foreach ($serviceCenter->reviewSources as $reviewSource)
                    <input class="feedback__checkbox" type="radio" name="tabset" id="tab-{{ $reviewSource->id }}"
                        {{ $reviewSource->id === 1 ? 'checked' : '' }} autocomplete="off" />
                    <label class="feedback__label" for="tab-{{ $reviewSource->id }}"
                        data-tab-path="tab-feedback-{{ $reviewSource->id }}" data-tab-group="tab-feedback">
                        <img src="{{ asset("resources-assets/svg/$reviewSource->logo") }}" alt="{{ $reviewSource->name }}" />
                        <span class="btn feedback__number">{{ $reviewSource->pivot->rating_count }}
                            {{ getNumEnding((int) $reviewSource->pivot->rating_count, ['оценка', 'оценки', 'оценок']) }}</span>
                        <x-display-rating rating="{{ $reviewSource->pivot->rating }}" disabled={{ true }}
                            classMod="feedback-review-source" />
                    </label>
                @endforeach
            </div>
        </div>
        <div class="feedback__panels">
            @foreach ($serviceCenter->reviewSources as $reviewSource)
                <div class="feedback__tab{{ $reviewSource->id == 1 ? ' open' : '' }}"
                    data-tab-target="tab-feedback-{{ $reviewSource->id }}" data-tab-group="tab-feedback">
                    @include('layouts.comments-list', [
                        'reviewSource' => $reviewSource,
                        'filterID' => "comments_filter_$reviewSource->id",
                    ])
                </div>
            @endforeach
        </div>
        <div class="feedback__list">
            @foreach ($serviceCenter->reviewSources as $reviewSource)
                <x-accordion id="feedback-item-{{ $reviewSource->id }}" modifier="feedback">
                    <x-slot name="title">
                        <img class="feedback__logo" src="{{ asset("resources-assets/svg/$reviewSource->logo") }}"
                            alt="{{ $reviewSource->name }}" />
                        <div class="feedback__info">
                            <x-display-rating rating="{{ $reviewSource->pivot->rating }}" disabled={{ true }}
                                classMod="feedback-review-source" />
                            <span class="btn feedback__number">{{ $reviewSource->pivot->rating_count }}
                                {{ getNumEnding((int) $reviewSource->pivot->rating_count, ['оценка', 'оценки', 'оценок']) }}</span>
                        </div>
                    </x-slot>
                    @include('layouts.comments-list', [
                        'reviewSource' => $reviewSource,
                        'filterID' => "mobile_comments_filter_$reviewSource->id",
                    ])
                </x-accordion>
            @endforeach
        </div>
    </div>
</section>
