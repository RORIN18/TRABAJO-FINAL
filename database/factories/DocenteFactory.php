<?php

namespace Database\Factories;

use App\Models\Docente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Docente>
 */
class DocenteFactory extends Factory
{
    protected $model = Docente::class;

    public function definition(): array
    {
        return [
              'codigo' => fake()->unique()->bothify('DOC-####'),
            'ci' => fake()->unique()->numerify('########'),
            'nombres' => fake()->firstName(),
            'apellidos' => fake()->lastName(),
            'correo' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('7#######'),
            'especialidad' => fake()->randomElement([
                'Programación',
                'Redes',
                'Electrónica',
                'Base de datos',
                'Contabilidad',
            ]),
            'activo' => true,
        ];
    }
}
