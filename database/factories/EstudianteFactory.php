<?php

namespace Database\Factories;

use App\Models\Carrera;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;
    public function definition(): array
    {
        return [
            'carrera_id' => Carrera::factory(),
                   'codigo' => fake()->unique()->bothify('EST-#####'),
            'ci' => fake()->unique()->numerify('########'),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'fecha_nacimiento' => fake()
                ->dateTimeBetween('-35 years', '-18 years')
                ->format('Y-m-d'),
            'correo' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('7#######'),
            'estado' => 'activo',
        ];
    }
}
