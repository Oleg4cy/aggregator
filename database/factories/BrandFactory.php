<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = 'brand';
        for ($i = 0; $i < 2; $i++) {
            $name .= '_' . fake()->word();
        }

        return [
            'name' => $name,
            'equipment_type_id' => \App\Models\EquipmentType::factory(),
        ];
    }
}
