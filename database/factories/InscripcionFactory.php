<?php
namespace Database\Factories;
use App\Models\{Estudiante,Inscripcion,Paralelo};
use Illuminate\Database\Eloquent\Factories\Factory;
class InscripcionFactory extends Factory {
    protected $model = Inscripcion::class;
    public function definition(): array {
        return [
            'estudiante_id'=>Estudiante::inRandomOrder()->first()?->id ?? Estudiante::factory(),
            'paralelo_id'=>Paralelo::inRandomOrder()->first()?->id ?? Paralelo::factory(),
            'fecha_inscripcion'=>fake()->dateTimeBetween('-6 months','now')->format('Y-m-d'),
            'estado'=>'inscrito',
        ];
    }
}