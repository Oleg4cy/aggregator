<?php
function seedReviewSources(): void
{
    $reviewSources = [
        ['Яндекс карты', 'yandex-logo.svg'],
        ['Google maps', 'google-logo.svg'],
        ['2Gis', '2gis-logo.svg'],
        ['Авито', 'avito-logo.svg']
    ];

    foreach ($reviewSources as $reviewSource) {
        \App\Models\ReviewSource::factory()->create([
            'name' => $reviewSource[0],
            'logo' => $reviewSource[1],
        ]);
    }
}
