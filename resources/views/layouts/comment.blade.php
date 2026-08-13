<div class="comment">
    <div class="comment__header">
        <div class="comment__photo">
            <img src="{{ asset('assets/img/item/customer-photo.jpg') }}" alt="Фото автора отзыва" />
        </div>
        <div class="comment__info">
            <p class="comment__name">{{ $comment->name }}</p>
            <p class="comment__date">{{ $comment->date }}</p>
            @if (isset($comment->rating) && is_numeric($comment->rating))
                <div class="comment__rating">
                    <x-star-rating
                        rating="{{ $comment->rating }}"
                        disabled={{ true }}
                    />
                </div>
            @endif
        </div>
    </div>
    <p class="comment__text">{{ $comment->text }}</p>
    @if(is_object($comment->response ?? null))
        <div class="comment__reply">
            <p class="comment__reply-date">
                Ответ от {{ $comment->response->date }}
            </p>
            <p class="comment__reply-text">
                {{ $comment->response->text }}
            </p>
        </div>
    @endif
</div>
