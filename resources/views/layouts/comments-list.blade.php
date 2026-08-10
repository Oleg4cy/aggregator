<section id="review-source-responses-{{ $reviewSource->id }}" class="comments-list">
    <div class="comments-list__header">
        <div id="{{ $filterID }}" class="comments-list__filter"></div>
        <a href="{{ $reviewSource->pivot->link }}" class="btn comments-list__path">Перейти в карточку сервисного центра</a>
        <a href="{{ $reviewSource->pivot->link }}" class="btn comments-list__path comments-list__path--mobile">Карточка сервисного центра</a>
    </div>
    @php($comments = json_decode($reviewSource->pivot->comments))
    <div class="comments-list__container">
        @foreach(is_array($comments) ? array_filter($comments, 'is_object') : [] as $comment)
            @include('layouts.comment', ['comment' => $comment])
        @endforeach
    </div>
</section>
