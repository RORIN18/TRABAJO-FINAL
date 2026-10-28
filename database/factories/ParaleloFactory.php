<?php
namespace Database\Factories;
use App\Models\{Docente,Gestion,Materia,Paralelo};
use Illuminate\Database\Eloquent\Factories\Factory;
class ParaleloFactory extends Factory {
    protected $model = Paralelo::class;
    public function definition(): array {
        return [
            'materia_id'=>Materia::inRandomOrder()->first()?->id ?? Materia::factory(),
            'gestion_id'=>Gestion::inRandomOrder()->first()?->id ?? Gestion::factory(),
            'docente_id'=>Docente::inRandomOrder()->first()?->id ?? Docente::factory(),
            'codigo'=>fake()->unique()->bothify('PAR-##'),
            'horario'=>fake()->randomElement(['Lunes y miércoles 08:00-10:00','Martes y jueves 10:00-12:00']),
            'cupo'=>fake()->numberBetween(25,35),
            'activo'=>true,
        ];
    }
}
