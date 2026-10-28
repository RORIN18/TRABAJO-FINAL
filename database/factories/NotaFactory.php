<?php
namespace Database\Factories;
use App\Models\{Inscripcion,Nota};
use Illuminate\Database\Eloquent\Factories\Factory;
class NotaFactory extends Factory {
    protected $model = Nota::class;
    public function definition(): array {
        return [
            'inscripcion_id'=>Inscripcion::inRandomOrder()->first()?->id ?? Inscripcion::factory(),
            'tipo'=>fake()->randomElement(['parcial_1','parcial_2','parcial_3','examen_final']),
            'calificacion'=>fake()->randomFloat(2,0,100),
            'fecha_evaluacion'=>fake()->dateTimeBetween('-3 months','now')->format('Y-m-d'),
        ];
    }
}