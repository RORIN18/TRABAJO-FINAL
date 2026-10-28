<?php

namespace Database\Factories;

use App\Models\Carrera;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Carrera>
 */
class CarreraFactory extends Factory
{
    protected $model = Carrera::class;
    public function definition(): array
    {
        return [
                'codigo' => fake()->unique()->bothify('CAR-####'),
            'nombre' => fake()->unique()->words(3, true),
            'duracion_anios' => 3,
            'descripcion' => fake()->sentence(),
            'activa' => true,
        ];
    }
}
