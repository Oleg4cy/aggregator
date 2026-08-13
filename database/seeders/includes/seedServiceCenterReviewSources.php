<?php
function seedServiceCenterReviewSources(): void
{
    $serviceCenters = \App\Models\ServiceCenter::all();
    $reviewSources = \App\Models\ReviewSource::all();

    foreach ($serviceCenters as $serviceCenter) {
        $ratings = [];

        foreach ($reviewSources as $reviewSource) {
            $comments = [];
            for ($i = 0; $i < rand(1, 7); $i++) {
                $comments[$i] = [
                    'name' => 'name_' . ($i + 1),
                    'date' => date('Y-m-d', mt_rand(1, time())),
                    'rating' => rand(1, 5),
                    'text' => implode('', fake()->paragraphs()),
                    'response' => [],
                ];
                if (rand(0, 1) > 0) {
                    $comments[$i]['response'] = [
                        'name' => 'name',
                        'date' => date('Y-m-d', mt_rand(1, time())),
                        'rating' => rand(11, 50) / 10,
                        'text' => implode('', fake()->paragraphs()),
                    ];
                }
            }

            $rating = rand(11, 50) / 10;
            $ratings[] = $rating;

            \Illuminate\Support\Facades\DB::table('service_center_review_source')->insert([
                'service_center_id' => $serviceCenter->id,
                'review_source_id' => $reviewSource->id,
                'external_id' => fake()->uuid(),
                'rating' => $rating,
                'rating_count' => rand(10, 100),
                'link' => '#',
                'comments' => json_encode($comments),
            ]);
        }

        if ($ratings !== []) {
            $serviceCenter->update([
                'average_rating' => round(
                    array_sum($ratings) / count($ratings),
                    2
                ),
            ]);
        }
    }
}
