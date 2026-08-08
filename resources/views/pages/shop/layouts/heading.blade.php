<section class="heading">
    <div class="container">
        <div class="heading__inner">
            <h1 class="heading__title">{{ $title }}</h1>
            <div class="heading__actions">
                <a href="#" class="btn btn--primary heading__btn">Заявка на оценку</a>
                @auth('web')
                    <a href="{{ route('platform.shop.edit', $shop) }}" class="btn btn--primary heading__edit">Редактировать</a>
                @endauth
            </div>
        </div>
    </div>
</section>
