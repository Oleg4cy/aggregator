<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquipmentType>
 */
class EquipmentTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = 'equipment_type';
        for ($i = 0; $i < 2; $i++) {
            $name .= '_' . fake()->word();
        }
        return [
            'name' => $name,
            'name_for_title' => $name,
        ];
    }
}
