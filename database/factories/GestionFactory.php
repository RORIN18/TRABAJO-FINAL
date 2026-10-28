<?php

namespace Database\Factories;

use App\Models\Gestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gestion>
 */
class GestionFactory extends Factory
{
    protected $model = Gestion::class;

    public function definition(): array
    {
           $inicio = fake()->dateTimeBetween('-2 years', '+1 year');
        $fin = (clone $inicio)->modify('+5 months');
        return [
                 'codigo' => fake()->unique()->randomElement([
                '2024-1', '2024-2',
                '2025-1', '2025-2',
                '2026-1', '2026-2',
                '2027-1', '2027-2',
                '2028-1', '2028-2',
            ]),
            'fecha_inicio' => $inicio->format('Y-m-d'),
            'fecha_fin' => $fin->format('Y-m-d'),
            'vigente' => false,
        ];
    }
}
