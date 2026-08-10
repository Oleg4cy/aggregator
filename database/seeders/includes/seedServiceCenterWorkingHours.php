<?php
function seedServiceCenterWorkingHours(): void
{
    $serviceCenters = \App\Models\ServiceCenter::all();
    foreach ($serviceCenters as $serviceCenter) {
        if ($serviceCenter->open_24_hours) {
            for ($day = 1; $day <= 7; $day++) {
                \Illuminate\Support\Facades\DB::table('service_center_working_hours')->insert([
                    'service_center_id' => $serviceCenter->id,
                    'day_of_week' => $day,
                    'is_open' => true,
                    'open_time' => null,
                    'close_time' => null,
                ]);
            }

            continue;
        }

        $firstOpen = false;
        $firstOpenTime = null;

        $nextDay = fake()->boolean(80); // 80% вероятность, что открыто
        $bDay = false;
        $bDayClose = false;

        for ($i = 1; $i <= 7; $i++) {
            $serviceCenterId = $serviceCenter->id;
            $day_of_week = $i;
            $is_open = $nextDay;
            if ($i == 7) $nextDay = false;
            else $nextDay = fake()->boolean(80);
            $minutesOpen = fake()->boolean() ? '30' : '00';
            $minutesClose = fake()->boolean() ? '30' : '00';
            if ($bDay && is_null($bDayClose)) {
                $open_time = null;
            } else if ($nextDay && !is_null($bDayClose)) {
                $open_time = rand(8, 12) . ':' . $minutesOpen;
            } else if (($is_open && !$bDay) || !$bDay) {
                $open_time = rand(8, 12) . ':' . $minutesOpen;
            } else {
                $open_time = fake()->boolean() ? rand(8, 12) . ':' . $minutesOpen : null; // 50% вероятность, что будет время открытия
            }
            if (!$nextDay || ($i == 7 && $firstOpen && !is_null($firstOpenTime))) {
                $close_time = rand(18, 21) . ':' . $minutesClose;
            } else if ($i == 7 && $firstOpen && is_null($firstOpenTime)) {
                $close_time = null;
            } else {
                $close_time = fake()->boolean() ? rand(18, 21) . ':' . $minutesClose : null; // 50% вероятность, что будет время закрытия
            }
            $bDay = $is_open;
            $bDayClose = $close_time;
            if ($i == 1) {
                $firstOpen = $is_open;
                $firstOpenTime = $open_time;
            }
            \Illuminate\Support\Facades\DB::table('service_center_working_hours')->insert([
                'service_center_id' => $serviceCenterId,
                'day_of_week' => $day_of_week,
                'is_open' => $is_open,
                'open_time' => $open_time,
                'close_time' => $close_time,
            ]);
        }
    }
}
