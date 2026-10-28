<?php

namespace Database\Factories;

use App\Models\Carrera;
use App\Models\Materia;
use Illuminate\Database\Eloquent\Factories\Factory;

class MateriaFactory extends Factory
{
    protected $model = Materia::class;

    public function definition(): array
    {
        return [
            'carrera_id' => Carrera::factory(),
            'codigo' => fake()->unique()->bothify('MAT-####'),
            'nombre' => fake()->unique()->words(3, true),
            'semestre' => fake()->numberBetween(1, 6),
            'creditos' => fake()->numberBetween(2, 6),
            'descripcion' => fake()->sentence(),
            'activa' => true,
        ];
    }
}